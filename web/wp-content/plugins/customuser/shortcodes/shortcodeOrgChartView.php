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


function orgchartview()
{
    $area = new Area();
    $array_area = $area->selectArea();
    $user = new User();
    $processo = new Processo();
    $array = array();
    $servizio = new Servizio();
    $procedimento = new Procedimento();
    $ufficio = new Ufficio();
    $fase = new Fase();
    $array_servizio = array();
    $array_procedimenti = array();
    $array_fasi_attivita = array();
    $temp_array = array();
    $tree_array = array();

    foreach ($array_area as $item) {
        $area_array = array('text' => $item[0], 'nodes' => array());

        $dirigenti = $user->selectDirigente($item[0]);

        foreach ($dirigenti as $dirigente) {

            $dirigente_array = array('text' => $dirigente, 'nodes' => array());
            $processi = $processo->findProjectByUser($dirigente);

            foreach ($processi as $proc) {
                $processo_array = array('text' => $proc, 'nodes' => array());

                array_push($dirigente_array['nodes'], $processo_array);
            }
            array_push($area_array['nodes'], $dirigente_array);

        }

        $servizi = $servizio->selectServizio($item[0]);

        foreach ($servizi as $serv) {
            $servizio_array = array('text' => $serv, 'nodes' => array());
            $array_po = $user->selectPO($item, $serv);

            foreach ($array_po as $po) {
                $po_array = array('text' => $po, 'nodes' => array());
                $procedimenti = $procedimento->findTaskByUser($po);

                foreach ($procedimenti as $procedim) {
                    $procedimento_array = array('text' => $procedim, 'nodes' => array());
                    $dipendenti_procedimenti = $user->selectDipendenteProcedimento($procedim);

                    foreach ($dipendenti_procedimenti as $dipendente_procedimento) {
                        $procedimento_dipendente_array = array('text' => $dipendente_procedimento, 'nodes' => array());
                        array_push($procedimento_array['nodes'], $procedimento_dipendente_array);
                    }

                    array_push($po_array['nodes'], $procedimento_array);
                }
                array_push($servizio_array['nodes'], $po_array);
            }


            $uffici = $ufficio->selectUfficio($item[0], $serv);
            foreach ($uffici as $uff) {
                $ufficio_array = array('text' => $uff, 'nodes' => array());
                $dipendenti = $user->selectDipendente($item[0], $serv, $uff);

                foreach ($dipendenti as $dipendente) {
                    $dipendenti_array = array('text' => $dipendente, 'nodes' => array());
                    $fasi_attivita = $fase->findFaseByUser($dipendente);

                    foreach ($fasi_attivita as $fase_attivita) {
                        $fase_attivita_array = array('text' => $fase_attivita, 'nodes' => array());

                        array_push($dipendenti_array['nodes'], $fase_attivita_array);
                    }
                    array_push($ufficio_array['nodes'], $dipendenti_array);
                }
                array_push($servizio_array['nodes'], $ufficio_array);

            }
            array_push($area_array['nodes'], $servizio_array);

        }


        array_push($tree_array, $area_array);


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
        <div class="col-sm-12">
            <h2>Tree</h2>
            <div id="treeview-searchable" class="treeview"></div>

        </div>
        <div class="col-sm-6">
            <h2>Results</h2>
            <div id="search-output"></div>
        </div>
    </div>
    <style>
        #treeview-searchable .node-disabled {
            display: none;
        }
    </style>

    <script>

        var organigramma_string = '<?php echo json_encode($tree_array);?>';
        console.log(organigramma_string);
        const organigramma = JSON.parse(organigramma_string);

        /*for (const [index, element] of dirigenti.entries()) {
            tree.push({
                text: dirigenti[index]['Area'],
                nodes: [
                    {
                        text: dirigenti[index]['Dirigente']
                    }
                ]
            });
        }
        tree = function (dirigenti, root) {
            var t = {};
            dirigenti.forEach(({ Area, Dirigente, Servizio }) => {
                Object.assign(t[Area] = t[Area] || {}, { label: Area, name: Dirigente });
                t[Servizio] = t[Servizio] || {};
                t[Servizio].children = t[Servizio].children || [];
                t[Servizio].children.push(t[Area]);
            });
            return t[root].children;
        }(dirigenti, null);
        console.log(tree);*/

        function getTree() {
            return organigramma;
        }


        var $searchableTree = $('#treeview-searchable').treeview({
            data: getTree(),
            levels: 6,
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

add_shortcode("post_orgchartview", "orgchartview");
