<?php
/*
Plugin Name: Orgchart view
Plugin URI:
Description:
Version: 0.1
Author: MG3
Author URI:

*/
include_once "../classes/OrgChartProcess.php";
include "../classes/User.php";


function processiorgchartview()
{
    $area = new Area();
    $array_area = $area->selectArea();
    $user = new User();
    $servizio = new Servizio();
    $ufficio = new Ufficio();
    $tree_array = array();
    $processo = new Processo();
    $procedimento = new Procedimento();
    $fase_attività = new Fase();
    $second_tree_array = array();
    $procedimenti_array = array();

    foreach ($array_area as $item) {
        $dirigenti = $user->selectDirigente($item[0]);

        foreach ($dirigenti as $dirigente) {
            $dirigente_array = array('text' => $dirigente, 'tags' => ['Dirigente di:', $item[0] . ' (Area)']);
            $processi = $processo->findProjectByUser($dirigente);

            foreach ($processi as $proc) {
                //TODO check dirigente, poichè più di uno possono essere responsabili dello stesso processo, per cui eliminare duplicati
                $processi_array = array('text' => $proc, 'tags' => ['Processo'], 'nodes' => array(), 'state' => array('expanded' => false));
                array_push($processi_array['nodes'], $dirigente_array);

                $servizi = $servizio->selectServizio($item[0]);
                foreach ($servizi as $serv) {
                    $array_po = $user->selectPO($item, $serv);

                    foreach ($array_po as $po) {
                        $po_array = array('text' => $po, 'tags' => ['PO di:', $serv . ' (Servizio)']);
                        $procedimenti = $procedimento->findTaskByUser($po, $proc);
                        foreach ($procedimenti as $procedim) {

                            $procedimenti_array = array('text' => $procedim, 'tags' => ['Procedimento'], 'nodes' => array(), 'state' => array('expanded' => false));
                            $array_dipendenti_assegnati = $user->selectDipendenteProcedimento($procedim);
                            $dipendenti_assegnati_array = array('text' => $array_dipendenti_assegnati, 'tags' => ['Dipendente Assegnato al Procedimento di:', $procedim]);
                            if (!empty($dipendenti_assegnati_array['text'])) {
                                array_push($procedimenti_array['nodes'], $dipendenti_assegnati_array);
                            }

                            array_push($procedimenti_array['nodes'], $po_array);

                            $uffici = $ufficio->selectUfficio($item[0], $serv);
                            foreach ($uffici as $uff) {
                                $array_dipendenti = $user->selectDipendente($item[0], $serv, $uff);
                                foreach ($array_dipendenti as $dipendente) {
                                    $dipendenti_array = array('text' => $dipendente, 'tags' => ['Dipendente di:', $uff . ' (Ufficio)']);
                                    $subtask = $fase_attività->findFaseByUser($dipendente, $procedim[0]);

                                    foreach ($subtask as $fase) {
                                        $subtask_array = array('text' => $fase, 'tags' => ['Fase - Attività'], 'nodes' => array(), 'state' => array('expanded' => false));
                                        array_push($subtask_array['nodes'], $dipendenti_array);
                                        array_push($procedimenti_array['nodes'], $subtask_array);
                                    }


                                }

                            }

                            array_push($processi_array['nodes'], $procedimenti_array);
                        }
                    }

                }

                array_push($second_tree_array, $processi_array);
            }


        }
        /*

             $uffici = $ufficio->selectUfficio($item[0], $serv);
             foreach ($uffici as $uff) {

                 $ufficio_array = array('text' => $uff, 'tags' => ['Ufficio'], 'nodes' => array(), 'state' => array('expanded' => false));
                 $dipendenti = $user->selectDipendente($item[0], $serv, $uff);

                 foreach ($dipendenti as $dipendente) {
                     $dipendenti_array = array('text' => $dipendente, 'tags' => ['Dipendente']);

                     array_push($ufficio_array['nodes'], $dipendenti_array);
                 }
                 array_push($servizio_array['nodes'], $ufficio_array);

             }*/
        // array_push($area_array['nodes'], $servizio_array);

    }


    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bootstrap 101 Template</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
              integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
              crossorigin="anonymous">

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
              integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ"
              crossorigin="anonymous">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.css"
              rel="stylesheet">

    <body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js"></script>


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
                    <input type="checkbox" class="checkbox" id="chk-ignore-case" value="false">
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
                    <input type="checkbox" class="checkbox" id="chk-reveal-results" value="false">
                    Reveal Results
                </label>
            </div>
            <button type="button" class="btn btn-success" id="btn-search">Search</button>
            <button type="button" class="btn btn-default" id="btn-clear-search">Clear</button>
            <!-- </form> -->
        </div>
        <div class="col-sm-12">
            <h2>Tree</h2>
            <div id="treeview-searchable" class="treeview">
                <ul class="list-group">
                    <li class="list-group-item node-treeview-searchable" data-nodeid="0"
                        style="color:undefined;background-color:undefined;"><span
                                class="icon expand-icon glyphicon glyphicon-plus"></span><span
                                class="icon node-icon"></span>Parent 1
                    </li>
                    <li class="list-group-item node-treeview-searchable" data-nodeid="5"
                        style="color:undefined;background-color:undefined;"><span class="icon glyphicon"></span><span
                                class="icon node-icon"></span>Parent 2
                    </li>
                    <li class="list-group-item node-treeview-searchable" data-nodeid="6"
                        style="color:undefined;background-color:undefined;"><span class="icon glyphicon"></span><span
                                class="icon node-icon"></span>Parent 3
                    </li>
                    <li class="list-group-item node-treeview-searchable" data-nodeid="7"
                        style="color:undefined;background-color:undefined;"><span class="icon glyphicon"></span><span
                                class="icon node-icon"></span>Parent 4
                    </li>
                    <li class="list-group-item node-treeview-searchable" data-nodeid="8"
                        style="color:undefined;background-color:undefined;"><span class="icon glyphicon"></span><span
                                class="icon node-icon"></span>Parent 5
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6">
            <h2>Results</h2>
            <div id="search-output"></div>
        </div>
    </div>
    <div id="tree"></div>
    <style>
        #treeview-searchable .node-disabled {
            display: none;
        }
    </style>

    <script>


        var processi_organigramma_string = '<?php echo json_encode($second_tree_array);?>';

        const processi_organigramma = JSON.parse(processi_organigramma_string);


        function getTree() {
            return processi_organigramma;
        }

        var $searchableTree = $('#treeview-searchable').treeview({
            data: getTree(),
            levels: 6,
            expandIcon: "fas fa-plus",
            collapseIcon: "fas fa-minus",
            state: {
                expanded: true,
            },
            showTags: true,


        });


        var search = function (e) {
            var pattern = $('#input-search').val();
            var options = {
                ignoreCase: $('#chk-ignore-case').is(':checked'),
                exactMatch: $('#chk-exact-match').is(':checked'),
                revealResults: $('#chk-reveal-results').is(':checked')
            };

            var results = $searchableTree.treeview('search', [pattern, options]);
            var output = '<p>' + results.length + ' matches found</p>';
            $.each(results, function (index, result) {
                output += '<p>- ' + result.text + '</p>';
            });
            $('#search-output').html(output);
        }

        $('#btn-search').on('click', search);
        $('#input-search').on('keyup', search);

        $('#btn-clear-search').on('click', function (e) {

            $searchableTree.treeview('clearSearch');
            $('#input-search').val('');
            $('#search-output').html('');
        });

        //$(function () {
        var selectors = {
            'tree': '#treeview-searchable',
            'input': '#input-search',
            'reset': '#btn-clear-search'
        };
        var lastPattern = ''; // closure variable to prevent redundant operation

        // collapse and enable all before search //
        function reset(tree) {
            tree.collapseAll();
            tree.enableAll();
        }

        // find all nodes that are not related to search and should be disabled:
        // This excludes found nodes, their children and their parents.
        // Call this after collapsing all nodes and letting search() reveal.
        //
        function collectUnrelated(nodes) {
            var unrelated = [];
            $.each(nodes, function (i, n) {
                if (!n.searchResult && !n.state.expanded) { // no hit, no parent
                    unrelated.push(n.nodeId);
                }
                if (!n.searchResult && n.nodes) { // recurse for non-result children
                    $.merge(unrelated, collectUnrelated(n.nodes));
                }
            });
            return unrelated;
        }

        // search callback
        var search = function (e) {
            var pattern = $(selectors.input).val();
            if (pattern === lastPattern) {
                return;
            }
            lastPattern = pattern;
            var tree = $(selectors.tree).treeview(true);
            reset(tree);
            if (pattern.length < 3) { // avoid heavy operation
                tree.clearSearch();
            } else {
                tree.search(pattern);
                // get all root nodes: node 0 who is assumed to be
                //   a root node, and all siblings of node 0.
                var roots = tree.getSiblings(0);
                roots.push(tree.getNode(0));
                //first collect all nodes to disable, then call disable once.
                //  Calling disable on each of them directly is extremely slow!
                var unrelated = collectUnrelated(roots);
                tree.disableNode(unrelated, {silent: true});
            }
        };

        // typing in search field
        $(selectors.input).on('keyup', search);

        // clear button
        $(selectors.reset).on('click', function (e) {
            $(selectors.input).val('');
            var tree = $(selectors.tree).treeview(true);
            reset(tree);
            tree.clearSearch();
        });

    </script>
    </body>
    </html>

    <?php


}

add_shortcode("post_processiorgchartview", "processiorgchartview");
