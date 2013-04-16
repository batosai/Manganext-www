<?php

require_once __DIR__ . '/bootstrap.php';

$t = new Opsone_Bots_Tonkam('http://www.tonkam.com/planning_manga.php');
$t->get();
exit;

///////////////// KANA //////////////////

Doctrine_Manager::connection()->close();

$kana = function() {
  Doctrine_Manager::connection()->connect();
  Doctrine_Manager::connection()->setCharset('utf8');

  $t = new Opsone_Bots_Kana('http://www.mangakana.com/sortie-manga/');
  $t->get();

  Doctrine_Manager::connection()->close();
};

$thread = new Opsone_Thread($kana);
$thread->start();

///////////////// GLENAT //////////////////

$glenat = function() {
  Doctrine_Manager::connection()->connect();
  Doctrine_Manager::connection()->setCharset('utf8');

  $t = new Opsone_Bots_Glenat('http://www.glenatmanga.com/a_paraitre.asp');
  $t->get();

  Doctrine_Manager::connection()->close();
};

$thread2 = new Opsone_Thread($glenat);
$thread2->start();

///////////////// PIKA //////////////////

$pika = function() {
  Doctrine_Manager::connection()->connect();
  Doctrine_Manager::connection()->setCharset('utf8');

  $t = new Opsone_Bots_Pika('http://www.pika.fr/new/releases');
  $t->get();

  Doctrine_Manager::connection()->close();
};

$thread3 = new Opsone_Thread($pika);
$thread3->start();

///////////////// AKATA //////////////////

$akata = function() {
  Doctrine_Manager::connection()->connect();
  Doctrine_Manager::connection()->setCharset('utf8');

  $t = new Opsone_Bots_Akata('http://www.akata.fr/planning.php');
  $t->get();

  Doctrine_Manager::connection()->close();
};

$thread4 = new Opsone_Thread($akata);
$thread4->start();

///////////////// KAZE //////////////////

$kaze = function() {
  Doctrine_Manager::connection()->connect();
  Doctrine_Manager::connection()->setCharset('utf8');

  $t = new Opsone_Bots_Kaze('http://www.kaze-manga.fr/index.php?option=com_content&view=category&layout=blog&id=94&Itemid=252');
  $t->get();

  Doctrine_Manager::connection()->close();
};

$thread5 = new Opsone_Thread($kaze);
$thread5->start();

///////////////// KAZE //////////////////

$kurokawa = function() {
  Doctrine_Manager::connection()->connect();
  Doctrine_Manager::connection()->setCharset('utf8');

  $t = new Opsone_Bots_Kurokawa('http://www.kurokawa.fr/site/page_accueil_site_editions_kurokawa&1.html');
  $t->get();

  Doctrine_Manager::connection()->close();
};

$thread6 = new Opsone_Thread($kurokawa);
$thread6->start();

///////////////// KI-OON //////////////////

$kioon = function() {
  Doctrine_Manager::connection()->connect();
  Doctrine_Manager::connection()->setCharset('utf8');

  $t = new Opsone_Bots_Kioon('http://www.ki-oon.com/planning.html');
  $t->get();

  Doctrine_Manager::connection()->close();
};

$thread7 = new Opsone_Thread($kioon);
$thread7->start();

///////////////// Taifu //////////////////

$taifu = function() {
  Doctrine_Manager::connection()->connect();
  Doctrine_Manager::connection()->setCharset('utf8');

  $t = new Opsone_Bots_Taifu('http://www.taifu-comics.com/index.php');
  $t->get();

  Doctrine_Manager::connection()->close();
};

$thread8 = new Opsone_Thread($taifu);
$thread8->start();

///////////////// Tonkam //////////////////

$tonkam = function() {
  Doctrine_Manager::connection()->connect();
  Doctrine_Manager::connection()->setCharset('utf8');

  $t = new Opsone_Bots_Tonkam('http://www.tonkam.com/planning_manga.php');
  $t->get();

  Doctrine_Manager::connection()->close();
};

$thread9 = new Opsone_Thread($tonkam);
$thread9->start();

///////////////// Doki-doki //////////////////

$dokidoki = function() {
  Doctrine_Manager::connection()->connect();
  Doctrine_Manager::connection()->setCharset('utf8');

  $t = new Opsone_Bots_Dokidoki('http://www.doki-doki.fr/modules/nouveautes_catalogue.php?moisalbum=');
  $t->get();

  Doctrine_Manager::connection()->close();
};

$thread10 = new Opsone_Thread($dokidoki);
$thread10->start();

///////////////// SOLEIL //////////////////

$soleil = function() {
  Doctrine_Manager::connection()->connect();
  Doctrine_Manager::connection()->setCharset('utf8');

  $t = new Opsone_Bots_Soleil('http://translate.googleusercontent.com/translate_c?anno=2&depth=1&hl=fr&rurl=translate.google.fr&sl=auto&tl=en&u=http://www.soleilmanga.com/planning.htm&usg=ALkJrhjJoslW1IHgabaXfCKXLTsEJNiuBQ');
  $t->get();

  Doctrine_Manager::connection()->close();
};

$thread11 = new Opsone_Thread($soleil);
$thread11->start();
