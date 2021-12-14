<?php

/*
Plugin Name: Chart Plugin
Plugin URI:
Description:
Version: 0.1
Author: MG3
Author URI:

*/


function visualize_orgchart()
{?>
    <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap 101 Template</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
 <link href="style.css" rel="stylesheet">
  <link href="bootstrap-treeview.css" rel="stylesheet">
   <link href="bootstrap-treeview.min.css" rel="stylesheet">
  <body>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="Tree.js"></script>
            <script src="bootstrap-treeview.js"></script>
                <script src="bootstrap-treeview.min.js"></script>
          </head>
    <div class="row">
            <hr>
            <h2>Searchable Tree</h2>
            <div class="col-sm-4">
              <h2>Input</h2>
              <!-- <form> -->
                <div class="form-group">
                  <label for="input-search" class="sr-only">Search Tree:</label>
                  <input type="input" class="form-control" id="input-search" placeholder="Type to search..." value="">
                </div>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" class="checkbox" id="chk-ignore-case" value="true">
    Ignore Case
                  </label>
                </div>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" class="checkbox" id="chk-exact-match" value="false">
    Exact Match
                  </label>
                </div>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" class="checkbox" id="chk-reveal-results" value="true">
    Reveal Results
</label>
                </div>
                <button type="button" class="btn btn-success" id="btn-search">Search</button>
                <button type="button" class="btn btn-default" id="btn-clear-search">Clear</button>
              <!-- </form> -->
            </div>
            <div class="col-sm-4">
              <h2>Tree</h2>
              <div id="treeview-searchable" class="treeview"></div>

            </div>
            <div class="col-sm-4">
              <h2>Results</h2>
              <div id="search-output"></div>
            </div>
          </div>

              <div id="tree"></div>
  </body>
</html>

    <?php
}

add_shortcode('post_orgchart', 'visualize_orgchart');
