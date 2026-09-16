<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TextoLegal;
use Illuminate\Database\Seeder;

class TextoLegalSeeder extends Seeder
{
    public function run(): void
    {
        $this->publicarSeAusente('termos', <<<'TEXTO'
1. Apresentação e aceitação

Estes Termos de Uso regulam o acesso ao Universidade Aquafast, plataforma gratuita de capacitação sobre produtos e serviços Aquafast destinada principalmente a distribuidores, representantes comerciais e clientes corporativos.

Ao criar uma conta e utilizar a plataforma, você declara que leu e concorda com estes Termos e com a Política de Privacidade. Caso não concorde, não prossiga com o cadastro nem utilize as áreas restritas.

2. Cadastro e acesso

Para solicitar acesso, é necessário informar nome, e-mail, senha e, opcionalmente, telefone. A conta nasce pendente e só é liberada após aprovação de um administrador. A aprovação não é automática e pode ser recusada quando os dados forem inconsistentes ou não permitirem confirmar a relação do solicitante com o público da plataforma.

Você deve fornecer informações verdadeiras e atualizadas, manter sua senha em sigilo e comunicar à Aquafast qualquer suspeita de uso indevido. A conta é pessoal e não pode ser compartilhada.

3. Uso permitido

O conteúdo deve ser utilizado para capacitação e consulta relacionadas aos produtos e serviços Aquafast. É proibido usar a plataforma para finalidade ilegal, tentar acessar contas ou áreas sem autorização, interferir no funcionamento do serviço, copiar dados de outros usuários, enviar conteúdo ofensivo ou malicioso, ou explorar o material para finalidade comercial não autorizada.

Perguntas e comentários devem se limitar ao assunto da aula e observar linguagem respeitosa. A Aquafast poderá moderar, ocultar ou remover conteúdo que viole estes Termos.

4. Conteúdo técnico e segurança

Os treinamentos têm finalidade educativa e informativa. Eles não substituem rótulos, fichas técnicas, fichas de dados de segurança, manuais, normas aplicáveis nem orientações oficiais mais recentes fornecidas pela Aquafast. Em caso de divergência, prevalece o documento oficial vigente do produto. O usuário é responsável por observar as regras de segurança, armazenamento, transporte e aplicação correspondentes à sua atividade.

5. Propriedade intelectual

Textos, vídeos, marcas, imagens, materiais e demais conteúdos disponibilizados na plataforma pertencem à Aquafast ou a seus respectivos licenciantes. O acesso não transfere direitos de propriedade intelectual. Sem autorização prévia, não é permitido reproduzir, adaptar, vender, sublicenciar, publicar ou distribuir esses conteúdos, ressalvados os usos admitidos pela legislação.

6. Serviços de terceiros

A plataforma pode incorporar vídeos e recursos fornecidos por terceiros, como o YouTube em modo de privacidade aprimorada. Esses serviços podem estar sujeitos a termos e políticas próprios. A Aquafast não controla a disponibilidade permanente de serviços externos.

7. Disponibilidade e alterações

A Aquafast procura manter a plataforma disponível e segura, mas poderá realizar manutenções, corrigir falhas, alterar funcionalidades ou atualizar conteúdos. Não se garante funcionamento ininterrupto, nem que todo material permanecerá disponível indefinidamente.

8. Suspensão ou encerramento da conta

A conta poderá ser bloqueada em caso de violação destes Termos, risco à segurança, uso indevido, solicitação do titular ou encerramento da relação que justificou o acesso. Quando cabível, os dados serão mantidos, eliminados ou anonimizados conforme a Política de Privacidade e a legislação aplicável.

9. Privacidade

O tratamento de dados pessoais relacionado ao cadastro e ao uso da plataforma está descrito na Política de Privacidade do Universidade Aquafast, que integra estes Termos.

10. Atualizações destes Termos

Estes Termos podem ser alterados para refletir mudanças legais, operacionais ou no serviço. A versão e a data de publicação ficam indicadas nesta página. Quando uma alteração material exigir novo aceite, o usuário será informado ao acessar a plataforma.

11. Legislação e contato

Estes Termos são regidos pela legislação brasileira, preservados os direitos que não possam ser limitados por contrato. Dúvidas sobre a plataforma podem ser encaminhadas para universidade@aquafast.com.br ou para os canais oficiais da Aquafast.
TEXTO);

        $this->publicarSeAusente('privacidade', <<<'TEXTO'
1. Objetivo

Esta Política de Privacidade explica como a Aquafast, responsável pelo Universidade Aquafast, trata dados pessoais de visitantes e usuários da plataforma. O tratamento observa a Lei nº 13.709/2018 (Lei Geral de Proteção de Dados Pessoais — LGPD) e os princípios de finalidade, adequação, necessidade, transparência, segurança e prevenção.

2. Dados tratados

Podemos tratar as seguintes categorias de dados:

• cadastro: nome, e-mail, senha protegida por hash e telefone opcional;
• perfil e vínculo profissional: empresa, cargo e organização, caso sejam informados posteriormente pelo usuário ou cadastrados pela administração;
• uso da plataforma: cursos, matrículas, aulas acessadas, progresso, conclusões, materiais baixados, perguntas, respostas e notificações;
• dados técnicos e de segurança: endereço IP do aceite, data e hora de acessos, dados de sessão, registros de erro e informações técnicas básicas do navegador e do dispositivo.

O cadastro público não solicita empresa, cargo, CPF, endereço, dados bancários nem dados pessoais sensíveis.

3. Finalidades e bases legais

Os dados são utilizados para:

• criar, analisar e administrar pedidos de acesso;
• autenticar usuários e proteger contas e a plataforma;
• oferecer cursos, registrar progresso e permitir materiais e interações;
• responder dúvidas, enviar avisos operacionais e recuperar senhas;
• produzir relatórios de participação e melhorar os treinamentos;
• prevenir fraude, abuso e incidentes de segurança;
• cumprir obrigações legais ou regulatórias e exercer direitos em processos.

Conforme o caso, o tratamento poderá se apoiar na execução de contrato ou de procedimentos preliminares, no legítimo interesse da Aquafast com avaliação dos direitos do titular, no cumprimento de obrigação legal ou regulatória e no exercício regular de direitos. Quando o consentimento for a base adequada, ele será solicitado de forma específica e poderá ser revogado nos termos da lei.

4. Compartilhamento e operadores

Os dados podem ser tratados por fornecedores que apoiam a hospedagem, banco de dados, envio de e-mails, segurança, suporte e reprodução de vídeos, sempre na medida necessária para prestar o serviço. Vídeos podem ser incorporados por youtube-nocookie.com. Também poderá haver compartilhamento com autoridades públicas quando houver obrigação legal, ordem válida ou necessidade de exercer direitos.

A Aquafast não vende dados pessoais. Fornecedores são selecionados de acordo com sua função e devem observar obrigações de confidencialidade, segurança e proteção de dados.

5. Transferência internacional

Alguns fornecedores de tecnologia podem armazenar ou processar dados fora do Brasil. Nesses casos, a Aquafast adotará mecanismos compatíveis com a LGPD e medidas adequadas para proteger os dados pessoais.

6. Cookies e tecnologias semelhantes

O Universidade Aquafast utiliza cookies estritamente necessários para sessão, autenticação, segurança e proteção contra falsificação de requisições. A versão inicial não utiliza cookies de publicidade nem ferramentas de análise comportamental de terceiros. Recursos externos incorporados podem aplicar tecnologias próprias conforme suas políticas.

7. Retenção e eliminação

Os dados são mantidos pelo período necessário para oferecer a plataforma, preservar o histórico de treinamento, cumprir obrigações legais, prevenir fraudes e exercer direitos. Quando não forem mais necessários, serão eliminados ou anonimizados, salvo quando a conservação for permitida ou exigida por lei.

Quando a exclusão da conta for atendida por anonimização, os identificadores diretos são removidos ou substituídos, a conta é bloqueada e comentários deixam de ser exibidos. Registros de matrícula e progresso podem ser preservados de forma não identificável para estatísticas agregadas e integridade do histórico.

8. Direitos do titular

Nos termos da LGPD, o titular pode solicitar, quando aplicável: confirmação do tratamento; acesso; correção; anonimização, bloqueio ou eliminação de dados desnecessários ou tratados em desconformidade; portabilidade; informação sobre compartilhamentos; revogação do consentimento; oposição; e revisão de decisões tomadas unicamente por tratamento automatizado.

O próprio usuário pode consultar e corrigir dados disponíveis em seu perfil e exportar uma cópia dos dados mantidos pela plataforma. Outras solicitações podem ser encaminhadas para universidade@aquafast.com.br ou para os canais oficiais da Aquafast. Poderemos pedir informações adicionais para confirmar a identidade do solicitante e proteger a conta.

9. Segurança

São adotadas medidas técnicas e administrativas proporcionais aos riscos, incluindo controle de acesso, senhas protegidas, comunicação segura em produção, registros de auditoria, materiais em armazenamento privado, backups e monitoramento. Nenhum sistema é totalmente imune a incidentes; situações relevantes serão tratadas conforme a legislação e os procedimentos aplicáveis.

10. Crianças e adolescentes

A plataforma é destinada ao público profissional e não foi concebida para o cadastro autônomo de crianças. Caso seja identificado tratamento inadequado de dados de criança ou adolescente, o responsável poderá contatar a Aquafast para análise e providências.

11. Atualizações e contato

Esta Política pode ser atualizada para refletir mudanças legais, operacionais ou tecnológicas. A versão e a data de publicação ficam indicadas nesta página. Alterações materiais serão comunicadas pelos meios disponíveis e, quando necessário, será solicitado novo aceite.

Dúvidas ou solicitações sobre privacidade podem ser enviadas para universidade@aquafast.com.br ou apresentadas pelos canais oficiais da Aquafast.
TEXTO);
    }

    private function publicarSeAusente(string $tipo, string $conteudo): void
    {
        TextoLegal::query()->firstOrCreate(
            ['tipo' => $tipo, 'versao' => 1],
            ['conteudo' => $conteudo, 'publicado_em' => now()],
        );
    }
}
