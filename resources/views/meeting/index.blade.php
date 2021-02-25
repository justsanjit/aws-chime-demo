<table>
    @if($message = session('message'))
        <h2>{{ $message }}</h2>
    @endif
    <br>
    <form action="/meetings" method="post">
        @csrf
        <input type="submit" value="Create Meeting" />
    </form>
    <br>
    @foreach($meetings as $meeting)
    <tr>
        <td>{{ $meeting->id }}</td>
        <td>{{ $meeting->meeting_id}} </td>
        <td>
            <form action="/meetings/{{ $meeting->id }}/join" method="post">
                @csrf
                <input type="submit" value="Join" />
            </form>
        </td>
    </tr>
    @endforeach
</table>
