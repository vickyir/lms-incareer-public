<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multiple Upload Files</title>

    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>


</head>
<body>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="file" id="fileInput" multiple><br>
        <button type="submit" id="uploadSubmission">Upload</button>
    </form>
    <script>
        // let fileInput = document.getElementById("fileInput");
        // let btnUpload = document.getElementById("uploadSubmission");

        // btnUpload.addEventListener("click", function(e) {
        //     e.preventDefault();

        //     const xhr = new XMLHttpRequest();
        //     const formData = new FormData();

        //     for(let file of fileInput.files) {
        //         formData.append("subFiles[]", file);
        //     }

        //     xhr.open("post", "insert_submission.php");
        //     xhr.send(formData);
        // });

        $("#uploadSubmission").click(function(e) {
            e.preventDefault();

            let fileData = document.getElementById("fileInput");
            let assignment_id = 2;

            let data = {
                assigId: assignment_id,
                count: fileData.files.length
            }

            $.ajax({
                url: "insert_submission.php",
                type: "post",
                data: data,
                success: function(data) {
                    let dataJson = JSON.parse(data);
                    // console.log(dataJson[0].submission_id);
                    for(i = 0; i < fileData.files.length; i++) {
                        let formData = new FormData();
                        formData.append("data", fileData.files[i]);
                        formData.append("submission_id", dataJson[i].submission_id);

                        $.ajax({
                            url: "upload_submission.php",
                            type: "post",
                            data: formData,
                            contentType: false,
                            cache: false,
                            processData: false,
                            success: function(data) {
                                console.log(data);
                            }
                        })
                    }
                }
            })
        });
    </script>
</body>
</html>