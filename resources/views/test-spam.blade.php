<form action="{{route('spam-post')}}" method="post">
    @csrf
    <input type="text" name="body">
    <button> Submit </button>
</form>
