<div class="column is_one_third">
  <form class="request-form" data-url="{{ route('request.update') }}">
    <input type="hidden" value="{{ $course->id }}" name="course_id">
    <input type="hidden" value="{{ $request->id }}" name="request_id">
    <input type="hidden" value="{{ $request->type }}" name="type">
    <h5 class="title is-5">{{ $request->type }}</h5>
    <label class="label">Total Hours</label>
    <div class="field">
      <p class="control is-expanded has-icons-left">
        <input name="hours_needed" class="input is-small" type="numeric" placeholder="Hours" value="{{ $request->hours_needed }}">
        <span class="icon is-small is-left">
          <i class="fa fa-clock-o"></i>
        </span>
      </p>
    </div>
    <label class="label">Number of People</label>
    <div class="field">
      <p class="control is-expanded has-icons-left">
        <input name="demonstrators_needed" class="input is-small" type="numeric" value="{{ $request->demonstrators_needed }}">
        <span class="icon is-small is-left">
          <i class="fa fa-users"></i>
        </span>
      </p>
    </div>
    <label class="label">Semesters</label>
    <div class="field">
      <label class="checkbox">
        <input name="semester_1" type="checkbox" @if ($request->semester_1) checked @endif>
        1
      </label>
      <label class="checkbox">
        <input name="semester_2" type="checkbox" @if ($request->semester_2) checked @endif>
        2
      </label>
      <label class="checkbox">
        <input name="semester_3" type="checkbox" @if ($request->semester_3) checked @endif>
        3
      </label>
    </div>
    <label class="label">Special Requirements</label>
    <div class="field">
      <textarea name="skills" class="textarea">{{ $request->skills }}</textarea>
    </div>
    <footer class="card-footer">
      <button class="button is-success card-footer-item submit-button">Save</button>
    </footer>
  </form>
</div>