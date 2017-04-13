<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title> Sandwich </title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" type="text/css" href="./style.css"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto|Shrikhand" rel="stylesheet"/>
</head>
<body>
    <section id="wrapper">
        <?php
            function woops() {
                echo "<header class=\"title\"><h1> Is that a sandwich? </h1></header>";
                echo "<main class=\"main\">";
                echo "<p>Hello, and welcome to \"Is that a sandwich?\"!</p>";
                echo "<p>Here, you'll be asked to judge some dishes and tell us if you consider them sandwiches or not!</p>";
                echo "<p id=\"start\"><button id=\"go\" onclick=\"window.location.search='?_=" . base64_encode(1) . "'\">start</button></p>";
                echo "</main>";
            }

            if (isset($_GET["_"]) && base64_decode($_GET["_"]) > 0) {
                $hostname = "### REDACTED ###";
                $database = "### REDACTED ###";
                $username = "### REDACTED ###";
                $password = "### REDACTED ###";

                $con = mysqli_connect($hostname, $username, $password, $database) or die("No DB connection!");

                $irequest = mysqli_real_escape_string($con, base64_decode($_GET["_"]));

                $query = "SELECT * FROM $database.sandwich_stats WHERE id = $irequest;";
                $result = mysqli_query($con, $query);
                $num_results = mysqli_num_rows($result);

                if ($num_results > 0) {
                    for ($i = 0; $i < $num_results; $i++) {
                        $row = mysqli_fetch_array($result);

                        echo "<header><h1>" . $row["name"] . "</h1></header>";
                        echo "<main><aside id=\"ingredients\"><ul>";

                        $ingredients_json = json_decode($row["ingredients"], true);

                        foreach ($ingredients_json as $key => $value) {
                            if (isset($value[1])) {
                                echo "<li class=\"ingredient\">"
                                   .     "<figure><img src=\"/resources/" . $value[0] . "\" draggable=\"false\"/></figure>"
                                   .     "<p class=\"name\">" . $key . "</p>"
                                   .     "<p class=\"amount\">" . $value[1] . "</p>"
                                   . "</li>";
                            }
                        }
                        echo "</ul></aside>";
                        echo "<section id=\"build\" onclick=\"this.dataset.spread = this.dataset.spread == 1 ? 0 : 1\">";

                        foreach ($ingredients_json as $key => $value) {
                            echo '<img src="/resources/' . $value[0] . '" draggable="false"/>';
                        }

                        echo "</section>";
                    }
                    mysqli_close($con);

                    echo "<script type=\"text/javascript\">\n";
                    echo     "let build = document.getElementById(\"build\");\n";
                    echo     "let images = build.children;\n";
                    echo     "for (let i = 0, l = images.length; i < l; i++) {\n";
                    echo         "let _t = -150 + (i * 75);\n";
                    echo         "images[i].style.zIndex = l - i;\n";
                    echo     "}\n";
                    echo "</script>";

                    echo "</main>
                        <footer>
                            <button id=\"reject\">
                                <svg viewBox=\"0 0 24 24\">
                                    <path d=\"M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z\" />
                                </svg>
                            </button>
                            <button id=\"accept\">
                                <svg viewBox=\"0 0 24 24\">
                                    <path d=\"M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z\" />
                                </svg>
                            </button>
                            <script type=\"text/javascript\">
                                (function() {
                                    let reject = document.getElementById(\"reject\");
                                    let accept = document.getElementById(\"accept\");

                                    let f = function(s) {
                                        reject.disabled = accept.disabled = \"disabled\";

                                        var request = new XMLHttpRequest();
                                        request.open(\"POST\", \"/_sandwich_mngr\", true);
                                        request.onreadystatechange = function() {
                                            if (request.readyState == XMLHttpRequest.DONE && +request.responseText > 0) {
                                                window.location.search = \"?_=" . base64_encode($irequest + 1) . "\";
                                            }
                                        }
                                        request.setRequestHeader(\"Content-Type\", \"application/x-www-form-urlencoded; charset=UTF-8\");
                                        request.send(\"_=" . base64_encode($irequest) . "&__=\" + s);
                                    }
                                    reject.onclick = function(){f(\"" . base64_encode(0) . "\")};
                                    accept.onclick = function(){f(\"" . base64_encode(1) . "\")};
                                })();
                            </script>
                        </footer>";
                } else {
                    woops();
                }
            } else {
                woops();
            }
        ?>
    </section>
</body>
</html>