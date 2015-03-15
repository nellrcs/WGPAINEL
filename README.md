# WGPAINEL
Painel administrativo agil.

## Configurando

* **config.php** (config) Onde sera definido a conexao e o usuario principal e senha do painel.  

## Front
* ###Criando arquivos

* **s_php/** (controller) Arquivos php que devem ter o mesmo nome do seu view html

* **s_html/** (view) Arquivos html que devem ter o mesmo nome do seu controller php

* **/index.php** (index) arquivo principal onde deve ser definido o nome de todas as paginas que foram criados os arquivos com o mesmo nome, na pasta **s_html/** deve haver um arquivo **index.html**  obrigatorio mas nao precisa ser declarado no **/index.php**



* ###Controller
  Os aquivos criados no diretorio **s_php/** fazem conexao com o banco de dados e alteram o viasual do seu (view.html) que esta na pasta **s_html/** nao eh obigatorio a criacao deste arquivo se o view nao precisar interagir com o php.


* ###View  
 Todo o codigo html do site deve ser salvo na pasta **s_html/** e os trechos setados variáveis de template ex.
 ```html
 <title>{_TITULO_SITE}</title>
 ```
 serão subtituidas pelo o seu controller na pasta **s_php/** , estas alteracoes sao feitas atraves da classe [Template](http://raelcunha.com/template.php).

 Agumas variaveis de template ja foram definidas pelo controller e nao devem ser redeclaradas.

 ```html
 <base href="{BASE}">

 <a href="{BASE}{LG}pagina">

 <div>{FONE_SITE}</div>

 <div>{EMAIL_SITE}</div>

 <div>{SITE_ENDERECO}</div>

 <div>{GOOGLE}</div>

 <a href="{SOCIAL_FACE}">

 <a href="{SOCIAL_TWITTER}">

 <a href="{SOCIAL_GOOGLE}">

 <a href="{SOCIAL_LINKEDIN}">

 <a href="{SOCIAL_YOUTUBE}">

 <a href="{SOCIAL_REDDIT}">
 ```


* ###Tradução
 Todas as palavras do site devem ser finidas no arquivo **s_php/linguagem/textos.php**  

 ```html
 <li>{_EMPRESA}</li>
 <li>{_PORTIFOLIO}</li>
 <li>{_CONTATO}</li>
 ```

 ```php
<?
 $palavas = array(
   "HOME"=> array(
     '_OLAVISITANTE' => "Olá visitante seja bem vindo!",
     ),
   "MENU"=> array(
     '_EMPRESA' => "EMPRESA",
     '_SERVICOS' => "SERVICOS",
     '_CLIENTES' => "CLIENTES",
     '_PORTIFOLIO' => "PORTIFÓLIO",
     '_ARTIGOS' => "ARTIGOS",
     '_CONTATO' => "CONTATO"
     )
 );
 ?>
 ```



* ###Paginação

 A paginacao pode ser feita com urls amigaveis atraves de includes de trechos html detro da index.html, a variavel de template que recebe este condigo esta definifa como **{INCLUIR_PAGINA}** onde ela for declarada sera inserido o codigo relativo ao nome da url que foi passada.
