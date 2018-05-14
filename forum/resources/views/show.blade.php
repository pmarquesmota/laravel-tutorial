@include('header')
id : {{$content->id}}<br>
content : {{$content->content}}<br>
created_at : {{$content->created_at}}<br>
updated_at : {{$content->updated_at}}<br>
<a href="/index.php">Home page</a>
@include('footer')

