    <form action="/exam/apply" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        &nbsp;&nbsp;姓名:&nbsp;&nbsp; &nbsp;&nbsp;    <input type="text" name="name" required><br/>
        身份证号: <input type="text" name="card" required><br/>
        身份证picture: <input type="file" name="picture" required><br/>
        调用接口: <input type="text" name="api" required><br/>
        <button type="submit" class="btn btn-default">APPLY</button>
    </form>
