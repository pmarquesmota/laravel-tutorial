<p>
	{{$content->content}}
	<form action="/index.php/show/" method="POST">
	   @csrf
	   <input type="hidden" name="id" value={{$content->id}}>
	   <input type="submit" value="Read">
	</form>
	<form action="/index.php/edit/" method="POST">
	   @method('PUT')
	   @csrf
	   <input type="hidden" name="id" value={{$content->id}}>
	   <input type="submit" value="Update">
	</form>
	<form action="/index.php/destroy/" method="POST">
	   @method('DELETE')
	   @csrf
	   <input type="hidden" name="id" value={{$content->id}}>
	   <input type="submit" value="Delete">
	</form>
</p>

