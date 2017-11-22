<template>
    <article class="media">
      <div class="media-left">
        <toggle-button :value="accepted" @change="toggleAccepted" />
      </div>
      <div class="media-content">
        <div class="content">
          <p>
            <span v-if="!application.academicSeen" class="icon"><i class="fa fa-star" title="New application"></i></span></small><strong>{{ application.studentName }}</strong> <small>{{ application.studentEmail }} <span v-if="application.hasContract" class="icon"><i class="fa fa-file-text-o" title="Has contract"></i></span></small>
            <br>
            {{ application.requestType }}
            <br>
            {{ application.studentNotes }}
          </p>
        </div>
      </div>
    </article>
</template>

<script>
module.exports = {
    props: ['application'],

    data() {
      return {
        accepted: this.application.is_accepted,
        hasErrors: false
      }
    },

    computed: {
      toggleUrl() {
        return '/application/' + this.application.id + '/toggle-accepted';
      }
    },

    methods: {
      toggleAccepted() {
        this.accepted = ! this.accepted;
        console.log(this.accepted);
        axios.post(this.toggleUrl)
          .then((response) => {
            console.log('yay');
          })
          .catch((error) => {
            this.hasErrors = true;
            console.log(error);
          })
      }
    }
}
</script>
