<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        form * {
            margin-bottom: 1vh;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li>
                <a href="/module">Modules</a>
            </li>
            <li>
                <a href="/project">Projects</a>
            </li>
            <li>
                <a href="/apikey">Api Keys</a>
            </li>
            <li>
                <a href="/logout">Log Out</a>
            </li>
        </ul>
    </nav>
    <h1>Add Project</h1>
    <form style="display: flex; flex-direction: column; align-items: flex-start;" action="/project/create" method="POST">
        @csrf
        <label for="name">Name:</label>
        <input name="name" id="name" type="text" required placeholder="Project Name">
        <label for="routerssid">Router WiFi Login:</label>
        <input name="routerssid" id="routerssid" type="text" required placeholder="Router WiFi login">
        <label for="routerpass">Router WiFi Password:</label>
        <input name="routerpass" id="routerpass" type="text" required placeholder="Router WiFi password">
        @foreach ($basemodules as $key => $basemodule)
        <div style="padding: 1em; border: solid black 1px;display: flex; flex-direction: column; align-items: flex-start;">
            <label>{{$key}} modules</label>
            @foreach ($basemodule as $modulename)
                @if ($key == "custom")
                    <label for="{{$modulename["name"]}}">{{$modulename["name"]}}</label>
                    <input type="checkbox" name="{{$key}}[]" id="{{$modulename["name"]}}" value="{{$modulename["id"]}}">
                @else
                    <label for="{{$modulename}}">{{$modulename}}</label>
                    <input type="checkbox" name="{{$key}}[]" id="{{$modulename}}" value="{{$modulename}}" checked>
                @endif
            @endforeach
        </div>
        @endforeach
        <input type="submit" value="Submit">
    </form>
</body>
</html>