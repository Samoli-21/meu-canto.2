<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\{Controller, Request, Response};

final class ChatController extends Controller
{
    public function page(Request $request): Response
    {
        return $this->view('chat/index', ['title' => 'Conversar com a Canto']);
    }

    public function reply(Request $request): Response
    {
        $message = trim((string) $request->input('message', ''));
        if ($message === '') return $this->json(['error' => 'Escreve uma mensagem para começarmos.'], 422);
        if ((function_exists('mb_strlen') ? mb_strlen($message) : strlen($message)) > 2000) {
            return $this->json(['error' => 'A mensagem é demasiado longa. Resume o que gostavas de partilhar.'], 422);
        }

        $historyRaw = (string) $request->input('history', '[]');
        $historyInput = json_decode($historyRaw, true);
        $history = [];
        if (is_array($historyInput)) {
            foreach (array_slice($historyInput, -12) as $entry) {
                if (!is_array($entry)) continue;
                $role = $entry['role'] ?? '';
                $content = trim((string) ($entry['content'] ?? ''));
                if (in_array($role, ['user', 'assistant'], true) && $content !== '' && strlen($content) <= 4000) {
                    $history[] = ['role' => $role, 'content' => $content];
                }
            }
        }

        $apiKey = trim((string) env('AI_API_KEY', ''));
        if ($apiKey === '') {
            return $this->json(['reply' => $this->fallback($message), 'mode' => 'guided']);
        }

        $endpoint = (string) env('AI_API_URL', 'https://api.openai.com/v1/chat/completions');
        if (strtolower((string) parse_url($endpoint, PHP_URL_SCHEME)) !== 'https') {
            return $this->json(['error' => 'O endpoint de IA deve utilizar HTTPS.'], 500);
        }
        $model = (string) env('AI_MODEL', 'gpt-4o-mini');
        $payload = [
            'model' => $model,
            'temperature' => 0.45,
            'max_tokens' => 450,
            'messages' => [
                ['role' => 'system', 'content' => 'És a Canto, assistente de acolhimento e exploração do Meu Canto, uma plataforma informativa angolana. Responde em português claro, caloroso e sem julgamento. Não diagnostiques, não prescrevas medicamentos e não te apresentes como psicóloga ou profissional de saúde. Ajuda a pessoa a nomear o que sente, sugere passos gerais e seguros e incentiva o contacto com profissionais qualificados quando adequado. Não prometas confidencialidade absoluta. Se a pessoa indicar perigo imediato, intenção de se magoar ou magoar alguém, prioriza a segurança: recomenda que procure imediatamente um serviço de urgência presencial, se aproxime de uma pessoa de confiança e não fique sozinha. Não inventes números de emergência nem serviços locais. Mantém respostas curtas e faz uma pergunta de cada vez.'],
                ...$history,
                ['role' => 'user', 'content' => $message],
            ],
        ];
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nAuthorization: Bearer {$apiKey}\r\n",
                'content' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'timeout' => 25,
                'ignore_errors' => true,
            ],
        ]);

        try {
            $raw = @file_get_contents($endpoint, false, $context);
            if ($raw === false) throw new \RuntimeException('Não foi possível contactar o serviço de IA.');
            $data = json_decode($raw, true);
            $reply = trim((string) ($data['choices'][0]['message']['content'] ?? ''));
            if ($reply === '') throw new \RuntimeException('O serviço de IA não devolveu uma resposta válida.');
            return $this->json(['reply' => $reply, 'mode' => 'ai']);
        } catch (\Throwable $e) {
            error_log('Canto AI request failed: ' . $e->getMessage());
            return $this->json(['reply' => 'Não consegui ligar ao serviço de conversa neste momento. Podemos continuar com os recursos do Meu Canto ou tentar novamente daqui a pouco.', 'mode' => 'fallback'], 502);
        }
    }

    private function fallback(string $message): string
    {
        $text = function_exists('mb_strtolower') ? mb_strtolower($message) : strtolower($message);
        if (preg_match('/suicid|matar[- ]?me|acabar com a vida|nao quero viver|não quero viver|fazer mal a mim|magoar[- ]?me|autoagress/i', $text)) {
            return 'Lamento que estejas a passar por algo tão pesado. A tua segurança é o mais importante neste momento. Se sentes que podes magoar-te ou estás em perigo imediato, procura agora um serviço de urgência presencial, aproxima-te de alguém em quem confies e pede-lhe para ficar contigo. Afasta-te, se conseguires, de objectos ou situações que possam aumentar o perigo. Não precisas de lidar com isto sozinho/a. O Meu Canto não é um serviço de emergência.';
        }
        if (str_contains($text, 'ansiedade') || str_contains($text, 'nervos')) {
            return 'Obrigado por partilhares isso comigo. A ansiedade pode aparecer de várias formas e não tens de a compreender toda de uma vez. Experimenta reparar no que está a acontecer no teu corpo e no pensamento que mais se repete. O que costuma acontecer antes de te sentires assim?\n\nNota: estou em modo de orientação básica. Para activar respostas de IA, configura AI_API_KEY no Render.';
        }
        if (str_contains($text, 'press') || str_contains($text, 'stress') || str_contains($text, 'trabalho')) {
            return 'Parece que estás a lidar com muita pressão. Podemos começar por separar o que precisa mesmo da tua atenção hoje do que pode esperar. Qual é a situação que está a ocupar mais espaço neste momento?\n\nNota: estou em modo de orientação básica. Para activar respostas de IA, configura AI_API_KEY no Render.';
        }
        if (str_contains($text, 'triste') || str_contains($text, 'sozinh')) {
            return 'Obrigado por confiares este pensamento. Não vou assumir o que estás a sentir, mas posso ajudar-te a explorar isso com calma. Há quanto tempo tens sentido isso e existe alguém de confiança com quem possas conversar?\n\nNota: estou em modo de orientação básica. Para activar respostas de IA, configura AI_API_KEY no Render.';
        }
        return 'Obrigado por começares esta conversa. Podemos ir devagar, sem precisares de explicar tudo de uma vez. O que gostavas que eu compreendesse melhor sobre o que estás a viver?\n\nNota: estou em modo de orientação básica. Para activar respostas de IA, configura AI_API_KEY no Render.';
    }
}
