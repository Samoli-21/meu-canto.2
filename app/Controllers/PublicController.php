<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Controller, Database, Request, Response, Session};

/** Páginas públicas visuais. Os materiais desta fase são exemplos editoriais. */
final class PublicController extends Controller
{
    private const TOPICS = [
        'ansiedade' => ['title'=>'Ansiedade','tone'=>'lavender','summary'=>'Quando a preocupação ocupa demasiado espaço, pode ser útil parar, perceber o que está a acontecer e procurar informação segura.','intro'=>'A ansiedade pode aparecer perante incerteza, pressão ou situações importantes. Cada pessoa sente-a de uma forma: no corpo, nos pensamentos ou nas escolhas do dia a dia.','signs'=>['Pensamentos que parecem não desligar','Tensão no corpo ou dificuldade em relaxar','Sensação de alerta perante situações comuns'],'steps'=>['Dá nome ao que estás a sentir, sem te julgares.','Experimenta abrandar o ritmo e fazer uma pausa breve.','Conversa com alguém de confiança sobre o que te preocupa.'],'seek'=>'Quando a ansiedade é persistente, interfere com o sono, os estudos, o trabalho ou as relações, conversar com um profissional de saúde pode ajudar a compreender as opções de apoio.'],
        'tristeza' => ['title'=>'Tristeza','tone'=>'blue','summary'=>'Há dias em que tudo pesa mais. Reconhecer a tristeza é uma forma de começar a cuidar de ti.','intro'=>'A tristeza faz parte da experiência humana e pode surgir depois de uma perda, uma desilusão, uma mudança ou sem uma razão evidente. Não existe uma única maneira certa de a sentir.','signs'=>['Vontade de estar mais recolhido','Menos energia para actividades habituais','Necessidade de tempo para processar acontecimentos'],'steps'=>['Permite-te sentir sem transformar isso numa culpa.','Mantém um pequeno contacto com alguém que te faça sentir seguro.','Escolhe uma actividade simples e possível para hoje.'],'seek'=>'Procura apoio profissional quando a tristeza se prolonga, se intensifica ou começa a impedir actividades importantes. Não precisas de esperar que a situação se torne insuportável.'],
        'stress' => ['title'=>'Stress e pressão','tone'=>'orange','summary'=>'Entre prazos, responsabilidades e expectativas, é importante reconhecer os sinais de sobrecarga.','intro'=>'O stress pode surgir quando as exigências parecem ultrapassar os recursos disponíveis. Estudo, trabalho, deslocações, responsabilidades familiares e preocupações financeiras podem acumular-se.','signs'=>['Irritabilidade ou dificuldade em concentrar-se','Sensação de estar sempre com pressa','Sono irregular ou cansaço frequente'],'steps'=>['Separa o que é urgente do que pode esperar.','Divide uma tarefa grande em passos pequenos.','Reserva uma pausa realista, mesmo que seja curta.'],'seek'=>'Se a pressão se mantém e afecta a tua saúde ou funcionamento diário, procura orientação junto de um profissional qualificado.'],
        'solidao' => ['title'=>'Solidão','tone'=>'green','summary'=>'Podemos sentir solidão mesmo rodeados de pessoas. A ligação começa, por vezes, com um gesto pequeno.','intro'=>'A solidão está relacionada com a forma como sentimos as nossas ligações, não apenas com o número de pessoas à nossa volta. Pode aparecer em fases de mudança, afastamento ou falta de compreensão.','signs'=>['Sensação de não ser compreendido','Dificuldade em pedir companhia','Afastamento de actividades e relações'],'steps'=>['Pensa numa pessoa com quem te sintas relativamente à vontade.','Propõe uma conversa ou actividade simples.','Procura espaços comunitários seguros e actividades de interesse.'],'seek'=>'Quando a solidão causa sofrimento intenso ou se associa a outras dificuldades, um profissional pode ajudar a explorar o que estás a viver.'],
        'cansaco' => ['title'=>'Cansaço e sobrecarga','tone'=>'yellow','summary'=>'Nem todo o cansaço se resolve com força de vontade. Às vezes, é um sinal de que precisas de apoio e recuperação.','intro'=>'A sobrecarga pode resultar de muitas exigências ao mesmo tempo, pouco descanso ou responsabilidades que não dão espaço para recuperar. O contexto de cada pessoa importa.','signs'=>['Sensação de esgotamento ao começar o dia','Dificuldade em decidir ou organizar tarefas','Menos disponibilidade emocional para os outros'],'steps'=>['Identifica uma exigência que possa ser renegociada.','Pede ajuda numa tarefa concreta.','Observa o que melhora quando existe tempo de descanso.'],'seek'=>'Se o cansaço é persistente, muito intenso ou acompanhado por alterações importantes no teu bem-estar, considera uma avaliação por um profissional de saúde.'],
        'mudanca' => ['title'=>'Mudanças e incerteza','tone'=>'pink','summary'=>'Começar de novo, mudar de cidade, terminar uma relação ou procurar trabalho pode trazer emoções diferentes ao mesmo tempo.','intro'=>'As transições fazem parte da vida. Mesmo mudanças desejadas podem trazer insegurança, saudade, entusiasmo e receio. Adaptar-se leva tempo e não acontece da mesma forma para todos.','signs'=>['Dúvidas sobre decisões e próximos passos','Saudade de rotinas ou pessoas','Mistura de entusiasmo e preocupação'],'steps'=>['Reconhece o que mudou e o que continua estável.','Define um próximo passo pequeno e concreto.','Mantém contacto com pessoas e actividades que te dão apoio.'],'seek'=>'Pedir apoio pode ser útil quando a mudança se torna difícil de gerir ou interfere de forma significativa com a tua vida.'],
    ];
    private const RESOURCES = [
        ['type'=>'Guia prático','format'=>'Leitura','title'=>'Como falar sobre o que estou a sentir','summary'=>'Sugestões para preparar uma conversa com alguém de confiança.','duration'=>'5 min de leitura','tone'=>'resource-mint','icon'=>'book'],
        ['type'=>'Exercício','format'=>'Prática','title'=>'Uma pausa para regressar ao presente','summary'=>'Um exercício breve de atenção ao momento, sem obrigação de o fazer perfeitamente.','duration'=>'2–3 min','tone'=>'resource-peach','icon'=>'leaf'],
        ['type'=>'Vídeo','format'=>'Ver','title'=>'Pequenos passos de autocuidado','summary'=>'Ideias realistas para incluir pequenas pausas na rotina.','duration'=>'4 min','tone'=>'resource-blue','icon'=>'play'],
        ['type'=>'Checklist','format'=>'Descarregar','title'=>'O que pode ajudar num dia difícil','summary'=>'Uma lista simples para pensar em pessoas, lugares e actividades de apoio.','duration'=>'1 página','tone'=>'resource-lilac','icon'=>'check'],
        ['type'=>'Áudio','format'=>'Ouvir','title'=>'Dois minutos de tranquilidade','summary'=>'Uma pausa guiada para abrandar e observar a respiração.','duration'=>'2 min','tone'=>'resource-sand','icon'=>'audio'],
        ['type'=>'Artigo','format'=>'Leitura','title'=>'Sono, descanso e saúde emocional','summary'=>'Uma introdução à relação entre descanso, rotina e bem-estar.','duration'=>'6 min de leitura','tone'=>'resource-green','icon'=>'moon'],
    ];
    public function home(Request $r): Response
    {
        // A página inicial mantém a experiência editorial mesmo que a consulta
        // aos destaques esteja temporariamente indisponível. O erro fica nos logs.
        $featured = [];
        try {
            $featured = Database::query("SELECT slug,titulo,resumo,imagem_url,tipo FROM conteudos_site WHERE estado='publicado' AND tipo IN ('destaque','artigo') ORDER BY ordem ASC, actualizado_em DESC LIMIT 3")->fetchAll();
        } catch (\Throwable $e) {
            error_log('[Meu Canto] Não foi possível carregar os destaques da página inicial: ' . $e->getMessage());
        }

        return $this->view('public/home', [
            'title' => 'Um espaço para compreender o que sentes',
            'topics' => self::TOPICS,
            'resources' => array_slice(self::RESOURCES, 0, 4),
            'featuredContents' => $featured,
        ]);
    }
    public function feelings(Request $r): Response { return $this->view('public/feelings',['title'=>'O que sinto','topics'=>self::TOPICS]); }
    public function topic(Request $r): Response { $slug=(string)($r->params['slug']??''); if(!isset(self::TOPICS[$slug])) return $this->view('errors/error',['status'=>404,'message'=>'Não encontrámos este tema. Explora outros assuntos ou volta ao início.'],404); return $this->view('public/topic',['title'=>self::TOPICS[$slug]['title'],'topic'=>self::TOPICS[$slug],'slug'=>$slug]); }
    public function resources(Request $r): Response
    {
        $type = trim((string)$r->input('tipo', ''));
        $published = [];
        try {
            $published = Database::query("SELECT slug,titulo,resumo,imagem_url,tipo FROM conteudos_site WHERE estado='publicado' AND tipo='recurso' ORDER BY ordem ASC, actualizado_em DESC")->fetchAll();
        } catch (\Throwable $e) {
            error_log('[Meu Canto] Não foi possível carregar os recursos editoriais: ' . $e->getMessage());
        }
        $editorial = array_map(static fn($row) => [
            'slug'=>$row['slug'], 'type'=>'Recurso da equipa', 'format'=>'Leitura',
            'title'=>$row['titulo'], 'summary'=>$row['resumo'] ?: 'Material informativo publicado pela equipa do Meu Canto.',
            'duration'=>'Conteúdo editorial', 'tone'=>'resource-mint', 'icon'=>'✦', 'image_url'=>$row['imagem_url']
        ], $published);
        $items = array_merge($editorial, self::RESOURCES);
        if ($type !== '') {
            $items = array_values(array_filter($items, static fn($item) =>
                (function_exists('mb_strtolower') ? mb_strtolower($item['format']) : strtolower($item['format'])) ===
                (function_exists('mb_strtolower') ? mb_strtolower($type) : strtolower($type))
            ));
        }
        return $this->view('public/resources', ['title'=>'Biblioteca de recursos', 'resources'=>$items, 'selectedType'=>$type]);
    }
    public function schoolWork(Request $r): Response { return $this->view('public/work',['title'=>'Escola, trabalho e vida']); }
    public function professionals(Request $r): Response
    {
        $items = [];
        $directoryAvailable = true;
        try {
            $items = Database::query("SELECT id,nome,email,telefone,especialidade,formacao,experiencia_anos,biografia,foto_url,municipio,provincia,modalidade,morada_consultorio FROM profissionais_saude WHERE estado='aprovado' ORDER BY nome ASC")->fetchAll();
        } catch (\Throwable $e) {
            $directoryAvailable = false;
            error_log('[Meu Canto] Não foi possível carregar o directório de profissionais: ' . $e->getMessage());
        }
        return $this->view('public/professionals', [
            'title' => 'Encontrar apoio profissional',
            'professionals' => $items,
            'directoryAvailable' => $directoryAvailable,
        ]);
    }

    public function submitProfessional(Request $r): Response
    {
        $name=trim((string)$r->input('nome','')); $email=strtolower(trim((string)$r->input('email',''))); $phone=trim((string)$r->input('telefone',''));
        $license=trim((string)$r->input('cedula_profissional','')); $specialty=trim((string)$r->input('especialidade','')); $bio=trim((string)$r->input('biografia',''));
        $education=trim((string)$r->input('formacao','')); $experience=(int)$r->input('experiencia_anos',0); $photo=trim((string)$r->input('foto_url',''));
        $municipality=trim((string)$r->input('municipio','')); $province=trim((string)$r->input('provincia','Luanda'));
        $modality=(string)$r->input('modalidade','presencial'); $office=trim((string)$r->input('morada_consultorio','')); $consent=(string)$r->input('consent','');
        if($name===''  || !filter_var($email,FILTER_VALIDATE_EMAIL) || $phone==='' || $license==='' || $specialty==='' || strlen($bio)<30 || $experience<0 || $experience>70 || $consent!=='1' || !in_array($modality,['presencial','online','ambos'],true) || ($photo!=='' && (!filter_var($photo,FILTER_VALIDATE_URL) || strtolower((string)parse_url($photo,PHP_URL_SCHEME))!=='https'))) {
            Session::flash('error','Revê os campos obrigatórios. A biografia deve ter pelo menos 30 caracteres.'); return $this->redirect('/profissionais#candidatura');
        }
        try {
            Database::query("INSERT INTO profissionais_saude (usuario_id,nome,email,telefone,cedula_profissional,especialidade,formacao,experiencia_anos,biografia,foto_url,municipio,provincia,modalidade,morada_consultorio,estado) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?, 'pendente')", [
                Session::get('user_id') ?: null,$name,$email,$phone,$license,$specialty,$education?:null,$experience>0?$experience:null,$bio,$photo?:null,$municipality?:null,$province?:'Luanda',$modality,$office?:null
            ]);
        } catch (\PDOException $e) {
            if ((string)$e->getCode()==='23000') { Session::flash('error','Já existe uma candidatura com este número de cédula.'); return $this->redirect('/profissionais#candidatura'); }
            throw $e;
        }
        Session::flash('ok','Candidatura recebida. O perfil só será publicado depois da verificação e aprovação.');
        return $this->redirect('/profissionais#directorio');
    }

    public function content(Request $r): Response
    {
        $slug=(string)($r->params['slug']??'');
        $item=Database::query("SELECT titulo,resumo,corpo,imagem_url,tipo,actualizado_em FROM conteudos_site WHERE slug=? AND estado='publicado' LIMIT 1",[$slug])->fetch();
        if(!$item) return $this->view('errors/error',['status'=>404,'message'=>'Este conteúdo não está disponível.'],404);
        return $this->view('public/content',['title'=>$item['titulo'],'item'=>$item]);
    }
    public function sos(Request $r): Response { return $this->view('public/sos',['title'=>'Procurar ajuda']); }
    public function about(Request $r): Response { return $this->view('public/about',['title'=>'Sobre o Meu Canto']); }
    public function privacy(Request $r): Response { return $this->view('public/legal',['title'=>'Privacidade e dados','heading'=>'A tua privacidade merece cuidado.','intro'=>'Esta página apresenta os princípios de privacidade que orientam o desenho do Meu Canto. A política final será publicada depois de revisão jurídica e validação dos fluxos reais de dados.','sections'=>[['title'=>'Recolha mínima','body'=>'A plataforma deverá solicitar apenas os dados necessários para cada funcionalidade, explicando a finalidade antes de qualquer recolha.'],['title'=>'Consentimento e escolhas','body'=>'A partilha de informação sensível, o histórico de conversas e os lembretes deverão depender de escolhas claras, específicas e revogáveis.'],['title'=>'Acesso e segurança','body'=>'Os acessos deverão ser limitados por função, com protecção dos documentos privados e registo das operações administrativas relevantes.'],['title'=>'Direitos e contacto','body'=>'Os procedimentos para exercer direitos sobre os dados serão descritos na política definitiva, após validação jurídica à luz da legislação aplicável em Angola.']]]); }
    public function accessibility(Request $r): Response { return $this->view('public/legal',['title'=>'Acessibilidade','heading'=>'Um espaço que procura chegar a mais pessoas.','intro'=>'A acessibilidade faz parte do desenho do Meu Canto. Esta primeira versão inclui medidas de navegação e leitura que continuarão a ser revistas e testadas.','sections'=>[['title'=>'Navegação por teclado','body'=>'Os principais links e controlos podem ser alcançados por teclado e apresentam um indicador visual de foco.'],['title'=>'Ecrãs pequenos','body'=>'A disposição adapta-se a computadores, tablets e telemóveis, com menu específico para dispositivos móveis.'],['title'=>'Movimento e legibilidade','body'=>'A interface respeita a preferência por movimento reduzido e utiliza contrastes reforçados nos botões e elementos de acção.'],['title'=>'Melhoria contínua','body'=>'A conformidade com WCAG 2.2 AA deverá ser confirmada por testes técnicos e com pessoas com diferentes necessidades de acesso.']]]); }
    public function search(Request $r): Response { $term=trim((string)$r->input('q','')); $results=[]; if($term!==''){ foreach(self::TOPICS as $slug=>$x){$h=$x['title'].' '.$x['summary'].' '.$x['intro']; if((function_exists('mb_stripos')?mb_stripos($h,$term):stripos($h,$term))!==false)$results[]=['title'=>$x['title'],'summary'=>$x['summary'],'url'=>url('/tema/'.$slug),'kind'=>'Tema'];} foreach(self::RESOURCES as $x){$h=$x['title'].' '.$x['summary']; if((function_exists('mb_stripos')?mb_stripos($h,$term):stripos($h,$term))!==false)$results[]=['title'=>$x['title'],'summary'=>$x['summary'],'url'=>url('/recursos'),'kind'=>$x['type']];}} return $this->view('public/search',['title'=>'Pesquisar','term'=>$term,'results'=>$results]); }
}
