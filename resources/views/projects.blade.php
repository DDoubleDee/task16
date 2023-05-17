<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects</title>
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
    <h1>Projects</h1>
    <a id="createnew" href="/project/create">Create New Project</a>
    <table>
        <tr id="head">
            <td>
                Name
            </td>
            <td>
            </td>
            <td>
            </td>
        </tr>
        @foreach ($projects as $project)
            <tr>
                <td>
                    {{$project->name}}
                </td>
                <td>
                    <a href="/project/archive/{{$project->id}}">Download Archive</a>
                </td>
                <td>
                    <a href="/project/delete/{{$project->id}}">Delete</a>
                </td>
            </tr>
        @endforeach
    </table>
</body>
</html>