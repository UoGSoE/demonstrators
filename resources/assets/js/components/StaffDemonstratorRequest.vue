<template>

<div class="column is_one_third">
  <form class="request-form">
    <input type="hidden" name="staff_id" v-model="this.staff_id">
    <h5 class="title is-5">
      {{ type }}
      <transition name="fade">
        <button
          aria-label="Remove request"
          id="withdraw-request"
          v-if="alreadyRequested"
          @click.prevent="withdrawRequest"
          class="button is-small is-gla-danger is-pulled-right is-outlined"
          :disabled="hasErrors"
          title="Remove Request" v-html="buttonText"
        >
        </button>
      </transition>
    </h5>
    <div v-if="errorNotif" class="notification is-danger">
      {{ errorNotif }}
    </div>
    <label class="label">Start Date</label>
    <div class="field">
      <p class="control is-expanded has-icons-left">
        <flat-pickr
          :config="config"
          v-model="start_date"
          placeholder="Select date"
          input-class="input is-small"
          name="start_date">
        </flat-pickr>
        <span class="icon is-small is-left">
          <i class="fa fa-calendar fa-calendar-vue"></i>
        </span>
      </p>
    </div>
    <label class="label">Total Hours Per Teaching Assistant</label>
    <div class="field">
      <p class="control is-expanded has-icons-left">
        <input
          v-model="hours_needed"
          name="hours_needed"
          class="input is-small"
          type="number"
          placeholder="Hours required"
          required
        >
        <span class="icon is-small is-left">
          <i class="fa fa-clock-o"></i>
        </span>
      </p>
    </div>
    <label class="label">Hours Training Per Teaching Assistant</label>
    <div class="field">
      <p class="control is-expanded has-icons-left">
        <input
          v-model="hours_training"
          name="hours_training"
          class="input is-small"
          type="number"
          placeholder="Hours training"
        >
        <span class="icon is-small is-left">
          <i class="fa fa-hourglass-half"></i>
        </span>
      </p>
    </div>
    <label class="label">Number of People</label>
    <div class="field">
      <p class="control is-expanded has-icons-left">
        <input
          v-model="demonstrators_needed"
          name="demonstrators_needed"
          class="input is-small"
          type="number"
          placeholder="People required"
          required
        >
        <span class="icon is-small is-left">
          <i class="fa fa-users"></i>
        </span>
      </p>
    </div>
    <label class="label">Semesters</label>
    <div class="field">
      <div class="control">
        <label class="checkbox">
          <input name="semester_1" type="checkbox" v-model="semester_1">
          Semester 1
        </label>
      </div>
    </div>
    <div class="field">
      <div class="control">
        <label class="checkbox">
          <input name="semester_2" type="checkbox" v-model="semester_2">
          Semester 2
        </label>
      </div>
    </div>
    <div class="field">
      <div class="control">
        <label class="checkbox">
          <input name="semester_3" type="checkbox" v-model="semester_3">
          Semester 3
        </label>
      </div>
    </div>
    <label class="label">Required Degree Level of TA</label>
    <div v-for="degreelevel in this.degreelevels" class="field">
      <div class="control">
        <label class="checkbox">
          <input name="degreelevels" type="checkbox" :value="degreelevel.id" v-model="chosendegreelevels">
            {{degreelevel.title}}
        </label>
      </div>
    </div>
    <label class="label">Special Requirements</label>
    <div class="field">
      <textarea v-model="skills" name="skills" class="textarea"></textarea>
    </div>
    <footer class="card-footer">
      <button
        id="submit-request"
        @click.prevent="saveRequest"
        class="button is-gla-success card-footer-item submit-button"
        :class="{ 'is-loading': isBusy, 'is-gla-danger': hasErrors }"
        :disabled="hasErrors || !isComplete"
      >
        {{saveButton}}
      </button>
    </footer>
  </form>
</div>

</template>

<script>
module.exports = {
    props: ['request', 'degreelevels'],

    data() {
      return {
        id: this.request.id,
        type: this.request.type,
        start_date: this.request.start_date,
        course_id: this.request.course_id,
        staff_id: this.request.staff_id,
        hours_needed: this.request.hours_needed,
        hours_training: this.request.hours_training,
        demonstrators_needed: this.request.demonstrators_needed,
        semester_1: this.request.semester_1,
        semester_2: this.request.semester_2,
        semester_3: this.request.semester_3,
        chosendegreelevels: this.request.degree_levels ? this.request.degree_levels.map(function(object) { return object['id']; }) : [],
        skills: this.request.skills,
        isBusy: false,
        hasErrors: false,
        hasAccepted: false,
        saveButton: 'Save',
        confirmWithdrawal: false,
        errorNotif: '',
        config: {
          dateFormat: "d/m/Y",
        },
      };
    },

    computed: {
      alreadyRequested() {
        return this.id;
      },

      hasSemesters() {
        return this.semester_1 || this.semester_2 || this.semester_3;
      },

      withdrawUrl() {
        return '/request/' + this.id + '/withdraw';
      },

      saveUrl() {
        return '/request';
      },

      emptyDatesUrl() {
        return '/request/empty-dates/';
      },

      requestData() {
        return {
          id: this.id,
          type: this.type,
          course_id: this.course_id,
          start_date: this.start_date,
          staff_id: this.staff_id,
          hours_needed: this.hours_needed,
          hours_training: this.hours_training,
          demonstrators_needed: this.demonstrators_needed,
          semester_1: this.semester_1,
          semester_2: this.semester_2,
          semester_3: this.semester_3,
          skills: this.skills,
          degree_levels: this.chosendegreelevels
        }
      },

      isComplete() {
        return this.hasSemesters && this.hours_needed && this.demonstrators_needed && this.start_date;
      },

      buttonText: function(){
        if (this.hasAccepted) {
         return 'Cannot delete - has accepted students';
        }
        if (this.confirmWithdrawal) {
          return 'Delete - Are you sure?'
        }
        return '<span class="icon"><i class="fa fa-trash" title="Remove request"></i></span>';
      }

    },

    methods: {
      saveRequest() {
        if (!this.isComplete) {
          return;
        }

        this.isBusy = true;

        axios.post(this.saveUrl, this.requestData)
          .takeAtLeast(300)
          .then((response) => {
            this.refreshRequest(response.data.request);
            this.checkForEmptyDates();
            this.saveButton = 'Saved!'
          })
          .catch((error) => {
            this.hasErrors = true;
            console.log(error);
            if (error.response.data.message == 'Cannot change hours of a request when an application has been accepted.') {
              this.errorNotif = error.response.data.message;
            }
          })
          .then(() => {
            this.isBusy = false;
          });
      },

      checkForEmptyDates() {
        axios.get(this.emptyDatesUrl + this.staff_id)
        .then((response) => {
          if (!response.data.result) {
            $(".empty-dates").hide();
          }
        })
      },

      withdrawRequest() {
        if (this.confirmWithdrawal != true) {
          this.confirmWithdrawal = true;
          return;
        }
        this.isBusy = true;

        axios.post(this.withdrawUrl)
          .takeAtLeast(300)
          .then((response) => {
            this.clearRequest();
          })
          .catch((error) => {
            this.hasErrors = true;
            this.hasAccepted = true;
            console.log(error);
          })
          .then(() => {
            this.isBusy = false;
          });
      },

      refreshRequest(data) {
        this.id = data.id;
        this.type = data.type;
        this.course_id = data.course_id;
        this.start_date = data.start_date;
        this.staff_id = data.staff_id;
        this.hours_needed = data.hours_needed;
        this.hours_training = data.hours_training;
        this.demonstrators_needed = data.demonstrators_needed;
        this.semester_1 = data.semester_1;
        this.semester_2 = data.semester_2;
        this.semester_3 = data.semester_3;
        this.skills = data.skills;
      },

      clearRequest() {
        this.id = '';
        this.start_date = '';
        this.hours_needed = '';
        this.hours_training = '';
        this.demonstrators_needed = '';
        this.semester_1 = '';
        this.semester_2 = '';
        this.semester_3 = '';
        this.skills = '';
        this.chosendegreelevels = [];
      },
    }
}
</script>
<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s
}
.fade-enter, .fade-leave-to {
  opacity: 0
}
</style>