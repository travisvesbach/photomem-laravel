@extends('layouts.app')

@section('title')
    About
@endsection

@section('content')
    <h2>Random and Today or Random</h2>

    <p>
        The purpose of this project is to serve a picture from today in history or a random picture from synced directories of pictures.
    </p>

    <h3>Directories and Pictures</h3>
    <p>
        To add directories, add them to (or symlink them in) <code>public/assets/sync/</code>.  Then go to the <a href="<%= Web.routes.path(:directoryIndex) %>">Directories</a> page to sync the added directories into the system.  Once directories are synced, you can ignore directories and sync pictures in the directories you choose.
    </p>
    <p>
        <strong>Sync Directories</strong>: syncs all the directories in <code>public/assets/sync/</code>.  Does not sync any pictures.<br>
    </p>
    <p>
        <strong>Sync/Resync</strong>: syncs all pictures in a directory and child directories, including child directories not in the system yet.<br>
    </p>
    <p>
        <strong>Ignore</strong>: sets the directory and all child directories to be ignored.  They won't be synced when a parent directory is synced.  Syncing ignored directories will unignore ignored parent directories as well.
    </p>

    <h3>URL Parameters</h3>
    <table>
        <thead>
            <th>Option</th>
            <th>Syntax</th>
        </thead>
        <tbody>
            <!-- <tr> -->
                <!-- <td>resize</td> -->
                <!-- <td>size=960x540</td> -->
            <!-- </tr> -->
            <tr>
                <td>today in history</td>
                <td>today=true</td>
            </tr>
            <tr>
                <td>crop</td>
                <td>crop=960x540</td>
            </tr>
            <tr>
                <td>grayscale</td>
                <td>color=gray or mode=gray or gray</td>
            </tr>
            <tr>
                <td>format</td>
                <td>format=png</td>
            </tr>
            <tr>
                <td>file of byte data</td>
                <td>format=bytes</td>
            </tr>
            <tr>
                <td>include date</td>
                <td>date=true or date</td>
            </tr>
            <tr>
                <td>orientation</td>
                <td>orientation=landscape or orientation=portrait</td>
            </tr>
        </tbody>
    </table>

    <h3>How to Get a Picture</h3>
    <p>
        There is one main url for getting pictures.<br>
        <a href="<%= Web.routes.path(:random) %>"><%= Web.routes.url(:random) %></a>
    </p>

    <p>
        Options from the table above can be appended to the end of the url above.
    </p>
    <p>
        <a href="<%= Web.routes.path(:random) %>?today=true&crop=960x540&color=gray&date=true"><%= Web.routes.url(:random) %>?today=true&crop=960x540&color=gray&date=true</a> returns a picture taken today in history or random and crops it to 960x540 in grayscale with the date taken.
    </p>

    <hr>

    <h2>Top Picture from a Subreddit</h2>
    <p>
        <a href="<%= Web.routes.path(:subreddit) %>"><%= Web.routes.url(:subreddit) %></a> can be used to get the top post's picture from a subreddit from a given time.  Some options that can be used for local pictures do not work for this feature.  In the case that a top post does not have a picture, a Not Found picture will be displayed instead.
    </p>

    <h3>Subreddit URL Parameters</h3>
    <table>
        <thead>
            <th>Option</th>
            <th>Syntax</th>
        </thead>
        <tbody>
            <!-- <tr> -->
                <!-- <td>resize</td> -->
                <!-- <td>size=960x540</td> -->
            <!-- </tr> -->
            <tr>
                <td>crop</td>
                <td>crop=960x540</td>
            </tr>
            <tr>
                <td>grayscale</td>
                <td>color=gray or mode=gray or gray</td>
            </tr>
            <tr>
                <td>format</td>
                <td>format=png</td>
            </tr>
            <tr>
                <td>subreddit</td>
                <td>sub=art</td>
            </tr>
            <tr>
                <td>timespan</td>
                <td>time=day or time=all</td>
            </tr>
        </tbody>
    </table>

    <h3>How to Get a Subreddit Picture</h3>
    <p>
        <a href="<%= Web.routes.path(:subreddit) %>?sub=art&time=all"><%= Web.routes.url(:subreddit) %>?sub=art&time=all</a> returns the "art" subreddit's top post of all time's picture.
    </p>

@endsection
