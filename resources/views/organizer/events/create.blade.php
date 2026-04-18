<h1>Create Event</h1>

<form method="POST" action="/organizer/events">
@csrf

<label>Event Title</label>
<input type="text" name="title">

<button type="submit">Create Event</button>

</form>
