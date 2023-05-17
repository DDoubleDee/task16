<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project</title>
    <link rel="stylesheet" href="{{URL::to('/')}}/css.css">
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
        <div id="row">
            <div>
                <label for="name">Name:&nbsp&nbsp</label> <br>
                <input name="name" id="name" type="text" required placeholder="Project Name">
            </div>
            <div>
                <label for="routerssid">Router WiFi Login:&nbsp&nbsp</label> <br>
                <input name="routerssid" id="routerssid" type="text" required placeholder="Router WiFi login">
            </div>
            <div>
                <label for="routerpass">Router WiFi Password:&nbsp&nbsp</label> <br>
                <input name="routerpass" id="routerpass" type="text" required placeholder="Router WiFi password">
            </div>
        </div>
        <div id="modules">
            @foreach ($basemodules as $key => $basemodule)
            <div style="padding: 1em; border: solid black 1px; flex-grow: 1; height: 53vh; overflow-y: auto;">
                <label>{{$key}} modules</label>
                @foreach ($basemodule as $modulename)
                <div class="module">
                    @if ($key == "custom")
                        <label for="{{$modulename["name"]}}">{{$modulename["name"]}}</label>
                        <input type="checkbox" name="{{$key}}[]" id="{{$modulename["name"]}}" value="{{$modulename["id"]}}">
                    @else
                        <label for="{{$modulename}}">{{$modulename}}</label>
                        <input type="checkbox" name="{{$key}}[]" id="{{$modulename}}" value="{{$modulename}}" checked>
                    @endif
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
        <input id="submit" type="submit" value="Submit">
    </form>
</body>
</html>