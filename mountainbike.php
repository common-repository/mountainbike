<?php
/*
Plugin Name: Mountainbike
Plugin URI: http://wordpress.org/extend/plugins/mountainbike/
Description: Adds a customizeable widget which displays the latest news by http://www.tomsbikecorner.de/
Version: 1.0
Author: Hans Mittermeier
Author URI: http://www.tomsbikecorner.de/
License: GPL3
*/

function mtbnews()
{
  $options = get_option("widget_mtbnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Mountainbike',
      'news' => '5',
      'chars' => '30'
    );
  }

  // RSS Objekt erzeugen 
  $rss = simplexml_load_file( 
  'http://news.google.de/news?pz=1&cf=all&ned=de&hl=de&q=mountainbike&cf=all&output=rss'); 
  ?> 
  
  <ul> 
  
  <?php 
  // maximale Anzahl an News, wobei 0 (Null) alle anzeigt
  $max_news = $options['news'];
  // maximale Länge, auf die ein Titel, falls notwendig, gekürzt wird
  $max_length = $options['chars'];
  
  // RSS Elemente durchlaufen 
  $cnt = 0;
  foreach($rss->channel->item as $i) { 
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?> 
    
    <li>
    <?php
    // Titel in Zwischenvariable speichern
    $title = $i->title;
    // Länge des Titels ermitteln
    $length = strlen($title);
    // wenn der Titel länger als die vorher definierte Maximallänge ist,
    // wird er gekürzt und mit "..." bereichert, sonst wird er normal ausgegeben
    if($length > $max_length){
      $title = substr($title, 0, $max_length)."...";
    }
    ?>
    <a href="<?=$i->link?>"><?=$title?></a> 
    </li> 
    
    <?php 
    $cnt++;
  } 
  ?> 
  
  </ul>
<?php  
}

function widget_mtbnews($args)
{
  extract($args);
  
  $options = get_option("widget_mtbnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Mountainbike',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  mtbnews();
  echo $after_widget;
}

function mtbnews_control()
{
  $options = get_option("widget_mtbnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Mountainbike',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  if($_POST['mtbnews-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['mtbnews-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['mtbnews-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['mtbnews-CharCount']);
    update_option("widget_mtbnews", $options);
  }
?> 
  <p>
    <label for="mtbnews-WidgetTitle">Widget Title: </label>
    <input type="text" id="mtbnews-WidgetTitle" name="mtbnews-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="mtbnews-NewsCount">Max. News: </label>
    <input type="text" id="mtbnews-NewsCount" name="mtbnews-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="mtbnews-CharCount">Max. Characters: </label>
    <input type="text" id="mtbnews-CharCount" name="mtbnews-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="mtbnews-Submit"  name="mtbnews-Submit" value="1" />
  </p>
  
<?php
}

function mtbnews_init()
{
  register_sidebar_widget(__('Mountainbike'), 'widget_mtbnews');    
  register_widget_control('Mountainbike', 'mtbnews_control', 300, 200);
}
add_action("plugins_loaded", "mtbnews_init");
?>