<template>
    <div class="column">
        <div class="content">
            <table class="table is-narrow">
                  <tr>
                    <th>Type</th>
                    <td>{{ type }}
                        <a
                          v-if="userHasAppliedFor"
                          class="button is-small is-success is-pulled-right"
                          :class="{ disabled: userHasBeenAccepted, 'is-loading': isBusy }"
                        >
                          Withdraw
                        </a>
                        <a
                          v-else
                          class="button is-small is-info is-pulled-right"
                          :class="{ 'is-loading': isBusy }"
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
            type: this.request.type,
            id: this.request.id,
            staffName: this.request.staffName,
            hoursNeeded: this.request.hours_needed,
            semesters: this.request.semesters,
            skills: this.request.skills,
            userHasAppliedFor: this.request.userHasAppliedFor,
            userHasBeenAccepted: this.request.userHasBeenAccepted,
            isBusy: false
        }
    },

    computed: {
        withdrawUrl() {
            return '/something/' + this.id + '/withdraw';
        },

        applyUrl() {
            return '/something/' + this.id + '/apply';
        }
    },

    methods: {
        withdraw() {
            this.isBusy = true;

            axios.post(this.withdrawUrl)
              .takeAtLeast(300)
              .then((response) => {
                console.log('withdrew');
                this.userHasAppliedFor = false;
              })
              .catch((error) => {
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
                console.log('applied');
                this.userHasAppliedFor = true;
              })
              .catch((error) => {
                console.log(error);
              })
              .then(() => {
                this.isBusy = false;
              });
        }
    }
}
</script>
