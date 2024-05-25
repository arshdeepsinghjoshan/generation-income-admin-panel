
<form action="{{ route('support.add') }}" method="post" id="support-update" enctype="multipart/form-data">
    @csrf
    <div class="row align-items-starts">
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Subject </label>
                <input type="text" class="form-control d-block" name="title" value="{{ old('title', $model->title) }}">
            </div>
            @error("Title")
            <p style="color:red;">{{ $errors->first("title")}}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Category </label>
                <select name="department_id" class="validate form-control" id="department_id">
                    @foreach($model->getDepartmentOption() as $department)
                    <option value="{{$department->id}}" {{$department->id == $model->department_id ? 'selected' : ''}}>{{$department->title}}</option>
                    @endforeach

                </select>
            </div>
            @error("department_id")
            <p style="color:red;">{{ $errors->first("department_id")}}</p>
            @enderror
        </div>
        <input type="hidden" name="id" id="id" value="{{ $model->id }}" required />
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Priority </label>
                <select name="priority_id" class="validate form-control" id="priority_id">
                    @if($model->getPriorityOptions())
                    @foreach($model->getPriorityOptions() as $key => $priority)
                    <option value="{{$key}}" {{ $key == $model->priority_id ? 'selected' : ''}}>{{$priority}}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            @error("priority_id")
            <p style="color:red;">{{ $errors->first("priority_id")}}</p>
            @enderror
        </div>

        <div class="col-xl-8 col-lg-8 col-md-6 col-8">
            <div class="mb-6 required">
                <label for="message">Message</label>
                <textarea rows="5" name="message" id="message" class="validate form-control">{{$model->message}} </textarea>
            </div>
            @error("message")
            <p style="color:red;">{{ $errors->first("message")}}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-4">
            <div class="mb-6">
                <label for="message">Image</label>
                <div class="input-group">
                    <input type="file" class="form-control ticket_images" name="images[]" multiple onchange="previewImages(event)">
                </div>
            </div>
            @error("image")
            <p style="color:red;">{{ $errors->first("image")}}</p>
            @enderror
        </div>
        <div class="preview-images"></div>
        <div class="col-lg-12">
            <div class="d-flex align-items-center justify-content-end">
                <div class="downoad-btns text-end my-4">
                    <button class="btn btn-primary text-white ms-2">@empty($model->exists) {{ __('Add') }} @else {{ __('Update') }} @endempty</button>

                </div>
            </div>
        </div>
    </div>
</form>

