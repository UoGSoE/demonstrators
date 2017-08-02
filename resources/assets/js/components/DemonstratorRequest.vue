<template>
    <div class="column">
        <div class="content">
            <table class="table is-narrow">
                  <tr>
                    <th>Type</th>
                    <td>{{ type }}
                        <a
                          v-if="userHasAppliedAlready"
                          :title="getButtonTitle"
                          class="button is-small is-success is-pulled-right"
                          :class="{'is-loading': isBusy, 'is-danger': hasErrors }"
                          :disabled="userHasBeenAccepted || hasErrors"
                          @click.prevent="withdraw"
                        >
                          Withdraw
                        </a>
                        <a
                          v-else
                          :title="getButtonTitle"
                          class="button is-small is-info is-pulled-right"
                          :class="{ 'is-loading': isBusy, 'is-danger': hasErrors }"
                          :disabled="hasErrors"
                          @click.prevent="apply"
                        >
                          Apply
                        </a>
                    </td>
                  </tr>
                  <tr>
                      <th>Academic</th>
                      <td>{{ staffName }}</td>
                  </tr>
                  <tr>
                      <th>Hours</th>
                      <td>{{ hoursNeeded }}</td>
                  </tr>
                  <tr>
                      <th>Semesters</th>
                      <td>{{ semesters }}</td>
                  </tr>
                  <tr v-if="skills">
                      <th>Special Requirements</th>
                      <td>{{ skills }}</td>
                  </tr>
              </table>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: ['request'],

    data() {
        return {
            id: this.request.id,
            type: this.request.type,
            staffName: this.request.staffName,
            hoursNeeded: this.request.hours_needed,
            semesters: this.request.semesters,
            skills: this.request.skills,
            userHasAppliedAlready: this.request.userHasAppliedFor,
            userHasBeenAccepted: this.request.userHasBeenAccepted,
            isBusy: false,
            hasErrors: false
        }
    },

    computed: {
        withdrawUrl() {
            return '/application/' + this.id + '/withdraw';
        },

        applyUrl() {
            return '/request/' + this.id + '/apply';
        },

        getButtonTitle() {
          if (this.hasErrors) {
            return 'There was an error - sorry';
          }
          if (this.userHasBeenAccepted) {
            return 'You cannot withdraw an application which has been accepted';
          }
          return '';
        }
    },

    methods: {
        withdraw() {
            this.isBusy = true;

            axios.post(this.withdrawUrl)
              .takeAtLeast(300)
              .then((response) => {
                this.userHasAppliedAlready = false;
              })
              .catch((error) => {
                this.hasErrors = true;
                console.log(error);
              })
              .then(() => {
                this.isBusy = false;
              });
        },

        apply() {
            this.isBusy = true;

            axios.post(this.applyUrl)
              .takeAtLeast(300)
              .then((response) => {
                this.userHasAppliedAlready = true;
              })
              .catch((error) => {
                this.hasErrors = true;
                console.log(error);
              })
              .then(() => {
                this.isBusy = false;
              });
        }
    }
}
</script>
