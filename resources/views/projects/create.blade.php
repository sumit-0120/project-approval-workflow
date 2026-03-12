@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="pagetitle">
        <h1>Data Tables</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Tables</li>
                <li class="breadcrumb-item active">Data</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

     @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <p id="message" style="color:green;"></p>

                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title">Add Project</h5>
                    </div>

                    <div class="card-body">
                        <form action="{{route('projects.store')}}" id="projectform" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Image Upload -->
                            <div class="form-group mb-4 text-center">
                                <div class="file-container">
                                    <!-- <img src="https://placehold.co/150x150?text=Upload+Image" id="preview"> -->
                                    <div id="preview" class="file-preview">
                                        Upload File
                                    </div>

                                    <label for="file" class="edit-icon">
                                        <i class="bi bi-upload"></i>
                                    </label>
                                    <input type="file" name="file" id="file" accept=".pdf,.doc,.docx,.zip">
                                </div>
                                <div class="error text-danger " id="file_error"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title" id="title">
                                <div class="error text-danger" id="title_error"></div>
                            </div>

                             <div class="form-group mb-3">
                                <label>Description</label>
                                <textarea class="form-control" name="description" id="description"></textarea>
                                <div class="error text-danger" id="description_error"></div>
                            </div>


                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>


<style>
.file-container {
    position: relative;
    width: 150px;
    height: 150px;
    margin: auto;
    cursor: pointer;
}

.file-preview {
    width: 100%;
    height: 100%;
    border: 2px solid #ccc;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 10px;
    font-size: 14px;
    background: #f8f8f8;
}

.edit-icon {
    position: absolute;
    bottom: 10px;
    right: 10px;
    font-size: 22px;
    background: white;
    border-radius: 50%;
    padding: 6px;
    color: green;
}

#file {
    display: none;
}


</style>


<script>
document.addEventListener("DOMContentLoaded", function() {

    const form = document.getElementById("projectform");
    const file = document.getElementById("file");
    const preview = document.getElementById("preview");

    file.addEventListener("change", function () {
        const selectedFile = this.files[0];
        if (selectedFile) {
            preview.innerHTML = selectedFile.name;
        }
    });

    form.addEventListener("submit", function(e) {

        const title = document.getElementById("title").value.trim();
        const description = document.getElementById("description").value.trim();
        const fileError = document.getElementById("file_error");
        const titleError = document.getElementById("title_error");
        const descriptionError = document.getElementById("description_error");

        fileError.textContent = "";
        titleError.textContent = "";
        descriptionError.textContent = "";

        let isValid = true;

        if (!file.files || file.files.length === 0) {
            fileError.textContent = "Please upload a file";
            isValid = false;
        }

        if (title === "") {
            titleError.textContent = "title is required";
            isValid = false;
        } 

        if (description === "") {
            descriptionError.textContent = "description is required";
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }

      });

});
</script>

@endsection
