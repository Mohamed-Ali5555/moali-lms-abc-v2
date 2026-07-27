<div class="row mb-3">
    <label for="user-name" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Name') }}<span class="text-danger ms-1">*</span></label>
    <div class="col-sm-8">
        <input type="text" name="name" class="form-control ol-form-control" id="user-name" @isset($instructor->name) value="{{ $instructor->name }}" @endisset required>
    </div>
</div>


<div class="row mb-3">
    <label for="user-phone" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Phone') }}</label>
    <div class="col-sm-8">
        <input type="text" name="phone" class="form-control ol-form-control" id="user-phone" @isset($instructor->phone) value="{{ $instructor->phone }}" @endisset>
    </div>
</div>

<div class="row mb-3">
    <label for="user-address" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Address') }}</label>
    <div class="col-sm-8">
        <input type="text" name="address" class="form-control ol-form-control" id="user-address" @isset($instructor->address) value="{{ $instructor->address }}" @endisset>
    </div>
</div>
<div class="row mb-3">
    <label for="photo" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('User image') }}</label>
    <div class="col-sm-8">
        <input type="file" name="photo" class="form-control ol-form-control" id="photo">
    </div>
</div>
