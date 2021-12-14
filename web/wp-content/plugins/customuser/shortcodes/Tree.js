//https://github.com/jonmiles/bootstrap-treeview

//https://raw.githubusercontent.com/jonmiles/bootstrap-treeview/master/public/js/bootstrap-treeview.js
//https://rawgit.com/jonmiles/bootstrap-treeview/master/public/js/bootstrap-treeview.js

//https://raw.githubusercontent.com/jonmiles/bootstrap-treeview/master/public/css/bootstrap-treeview.css
//https://rawgit.com/jonmiles/bootstrap-treeview/master/public/css/bootstrap-treeview.css

// Dependencies
//Bootstrap v3.3.4 (>= 3.0.0)
//jQuery v2.1.3 (>= 1.9.0)


// Add this suggested feature to hide non-matched elements on the search (parent chain should remain visible)
// https://github.com/jonmiles/bootstrap-treeview/issues/101

var tree = [
    {
        text: "Parent 1",
        nodes: [
            {
                text: "Child 1",
                nodes: [
                    {
                        text: "Grandchild 1"
                    },
                    {
                        text: "Grandchild 2"
                    }
                ]
            },
            {
                text: "Child 2"
            }
        ]
    },
    {
        text: "Parent 2"
    },
    {
        text: "Parent 3"
    },
    {
        text: "Parent 4"
    },
    {
        text: "Parent 5"
    }
];

function getTree() {
    // Some logic to retrieve, or generate tree structure
    return tree;
}


var $searchableTree = $('#treeview-searchable').treeview({
    data: getTree(),
});

/*
var search = function(e) {
  var pattern = $('#input-search').val();
  var options = {
    ignoreCase: $('#chk-ignore-case').is(':checked'),
    exactMatch: $('#chk-exact-match').is(':checked'),
    revealResults: $('#chk-reveal-results').is(':checked')
  };
  var results = $searchableTree.treeview('search', [ pattern, options ]);

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
*/

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
//});
