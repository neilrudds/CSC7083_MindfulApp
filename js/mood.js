function loadMoodData() {
    $.ajax({
        type: "POST",
        url: "data/loadUserMoods.php",
        success: function (response) {
            $('#mood-data').html(response);
        },
        error: function (response) {
            alert(response);
        }
    });
}

$(document).ready(function () {

    $("#addMoodForm").submit(function (e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.

        $.ajax({
            type: "POST",
            url: "data/saveMood.php",
            cache: false,
            data: $('form#addMoodForm').serialize(),
            success: function (response) {
                $("#add-mood").html(response)
                $("#add-modal").modal('hide');
                loadMoodData(); // Refresh our data
            },
            error: function (response) {
                alert(response); // show response from the php script.
            }
        });

    });

    $(document).on("click", ".edit", function (e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.

        // Set the edit modal form fields with the data/attributes from the selected mood log card
        $("#editMoodLogId").val($(this).data("id"));
        $("#editComment").val($(this).attr("data-comment"));
        $("#editMood").val($(this).attr("data-moodid"));
    });

    $("#editMoodForm").submit(function (e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.

        $.ajax({
            type: "POST",
            url: "data/editMood.php",
            cache: false,
            data: $('form#editMoodForm').serialize(),
            success: function (response) {
                $("#edit-mood").html(response)
                $("#edit-modal").modal('hide');
                loadMoodData(); // Refresh our data
            },
            error: function (response) {
                alert(response); // show response from the php script.
            }
        });

    });

    $(document).on("click", ".delete", function (e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        $("#deleteMoodLogId").val($(this).data("id"));
    });

    $("#deleteMoodForm").submit(function (e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.

        $.ajax({
            type: "POST",
            url: "data/deleteMood.php",
            data: $('form#deleteMoodForm').serialize(),
            cache: false,
            success: function (response) {
                $("#delete-mood").html(response)
                $("#delete-modal").modal('hide');
                loadMoodData(); // Refresh our data
            },
            error: function (response) {
                alert(response); // show response from the php script.
            }
        });
    });
});