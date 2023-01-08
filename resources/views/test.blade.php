<!DOCTYPE html>
<html>
<head>
    <style>
        h2 {
            position: absolute;
            left: 100px;
            top: 150px;
        }
    </style>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/caret/1.3.7/jquery.caret.min.js" integrity="sha512-DR6H+EMq4MRv9T/QJGF4zuiGrnzTM2gRVeLb5DOll25f3Nfx3dQp/NlneENuIwRHngZ3eN6w9jqqybT3Lwq+4A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>--}}
    <script defer src="{{asset('js/caret.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $('#body').keydown(function (){
                let postion = $('#body').caret('offset');
                let left = postion.left
                let top = postion.top

                $('#list').css("left", left);
                $('#list').css("top", top);

                console.log(postion)
            })
        });
    </script>

</head>
<body>
<div style="position:relative">
    <textarea style="width: 100%" name="" id="body" cols="30" rows="10"></textarea>
    <p id="list" style="background-color: blue; position:absolute">test</p>
</div>

</body>
</html>
