<?php
function printOrgChartProcess(){

    $oc = new OrgChartProcess();
    
?>

<!DOCTYPE html>
    <html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            ul, #myUL {
                list-style-type: none;
            }

            li {
                list-style-type: none;
            }

            #myUL {
                margin: 0;
                margin-left: auto;
                margin-right: auto;
                padding: 0;
            }

            .caret::before {
                content: "\25B6";
                color: darkslategray;
                display: inline-block;
                margin-right: 6px;

            }

            .caret-down::before {
                transform: rotate(90deg);

            }

            .nested {
                display: none;
            }

            .active {
                display: block;
            }

            .processo{
                color:#483D8B ;
            }

            .procedimento{
                color: green;
            }

            .fase{
                color: #e36d11;
            }

            .atto{
                color: #a10000;
            }
        </style>
    </head>
    <body>
    <ul id="myUL">

    <?php
    foreach ($oc->getData() as $processo => $listaProcedimenti) {
        echo "
        <li>
            <span class='caret processo'>  $processo </span>
            <ul class='nested'>";
        foreach ($listaProcedimenti as $procedimento => $subTasks) {
            echo"
                <li>
                   <span class='caret procedimento' > $procedimento </span>
                   <ul class='nested'>
            ";
            foreach ($subTasks["fasi"] as $key => $fase){
                echo"
                        <li>
                           <span class='caret fase' > $fase</span> 
                        </li> ";
            }
            foreach ($subTasks["atti"]  as $key => $atto){
                echo"
                        <li>
                           <span class='caret atto' > $atto</span> 
                        </li> ";
            }
            echo " </ul>
                </li>";
        }
        echo "
            </ul>
        </li>";
    } ?>

    </ul>
    </body>
    <script>
        let toggler = document.getElementsByClassName("caret");
        console.log(toggler)

        for (let i = 0; i < toggler.length; i++) {
            toggler[i].addEventListener("click", function () {
                this.parentElement.querySelector(" .nested").classList.toggle("active");
                this.classList.toggle("caret-down");
            });
        }
    </script>

    </body>
    </html>

    <?php
}

add_shortcode("post_printorgchartprocess", "printOrgChartProcess");