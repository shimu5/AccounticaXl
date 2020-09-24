<html>
<head>

    <title>Error Tester</title>
    <style type="text/css">
        ul {
            list-style: none;
            margin: 0;
            padding: 0;
            margin-right: -1px;
        }

        li {
            margin: 0;
            padding: 0;
            margin-right: -1px;
        }

        li a {
            padding: 10px;
            display: block;

        }

        li a, li a:visited, li a:active {
            color: #000000;
            text-decoration: none;
        }

        li a.selected {
            background: #EEE;
            /*color: white;*/
            border-left: 10px solid #999;
            border-top: 1px solid #999;
            border-bottom: 1px solid #999;
            border-right: 0px solid #EEE;
            padding-left: 5px;
            margin: -1px;

        }

        #menu {
            position: absolute;
            left: 0;
            width: 199px;
            top: 0;
            bottom: 0;
            overflow-x: hidden;
            overflow-y: auto;
            background: #FFE5E5;
            border-right: 1px solid #999;
        }

        #frame {
            position: absolute;
            left: 200px;
            right: 0;
            top: 0;
            bottom: 0;
        }

        iframe {
            border: 0 none;
        }
    </style>
</head>

<body>
    <div id="menu">
        <ul>
            <li><a href="user-triggered">User triggered errors</a></li>
            <li><a href="parse-error">Parse error: T_NUMBER</a></li>
            <li><a href="parse-error-2">Parse error: T_PAAMAYIM_NEKUDOTAYIM</a></li>
            <li><a href="undefined-function">FATAL ERROR: Call to undefined function</a></li>
            <li><a href="non-object">FATAL ERROR: Access method of non object</a></li>
            <li><a href="memory-exhaust">FATAL ERROR: Allowed memory size exhausted</a></li>
            <li><a href="require">FATAL ERROR: Require</a></li>
            <li><a href="include">WARNING: Include</a></li>
            <li><a href="notice">NOTICE: variable undefined</a></li>
            <li><a href="deprecated">DEPRECATED: call deprecated function</a></li>
            <li><a href="strict">DEPRECATED: variable undefined</a></li>
            <li><a href="exception">Exceptions</a></li>
        </ul>
    </div>

    <div id="frame">
        <iframe id="childframe" style="width: 100%; height: 100%;">

        </iframe>
    </div>

    <script type="text/javascript">
        var elements = document.getElementsByTagName('a');
        var iframe = document.getElementById('childframe');

        var old;

        function clickme() {
            iframe.src = this.href;

            if (old) old.className = "";
            this.className = "selected";

            old = this;
            return false;
        };

        for (i in elements) {
            elements[i].onclick = clickme;
        }
    </script>
</body>
</html>
