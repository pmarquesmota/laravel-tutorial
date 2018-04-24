@include('header')
<form action="/index.php/store/" method="POST">
    @csrf
    <textarea name="content" placeholder="enter your comment here"></textarea>
    <input type="submit" value="post">
</form>
@include('footer')

