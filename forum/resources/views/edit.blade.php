@include('header')
<form action="/index.php/update/" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" value="{{$content->id}}" >
    <textarea name="content">{{$content->content}}</textarea>
    <input type="submit" value="Update">
</form>
@include('footer')

