<form action="/student/add" method="POST">
    <div class="input-group">
        <label for="">Student Name</label>
        <input type="text" name='name' class="form-control" placeholder="Name">
    </div>
    <div class="input-group">
        <label for="">Student Age</label>
        <input type="text" name='age' class="form-control" placeholder="Age">
    </div>
    <button>Add student</button>
</form>
<script>
const button = document.querySelector("button")

button.addEventListener("click", sendStudentData)
async function sendStudentData(e) {
    e.preventDefault();
    const name = document.querySelector("input[name='name']")
    const age = document.querySelector("input[name='age']")
    const data = {
        name: name.value,
        age: age.value
    };
    const POST = "POST";

    const response = await fetch("/student/add", {
        method: POST,
        body: JSON.stringify(data)
    });
    console.log(response);
}
</script>