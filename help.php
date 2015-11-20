 <!--taken from http://code.runnable.com/U314bamP1FkG5tH1/parsedown-demo-a-parser-for-markdown-in-php-->
<html>
<head>
  <title>Documentation</title>
  <link rel="stylesheet" href="https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/css/bootstrap.css">
</head>
<body>
  <div class='container'>
  <div class="markdown">
      <?php 
          // include markdown content as seperate file for maintainability
          require_once("help_markdown.php");
          
          //setup parsedown
          require_once("Parsedown.php");
          $parsedown = new parsedown();
          
          //parse markdown as html
          echo $parsedown->text($markdown); 
      ?>
  </div>
  </div>
</body>
</html>