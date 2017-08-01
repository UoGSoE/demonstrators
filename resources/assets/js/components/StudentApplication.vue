<template>
    <article class="media">
      <div class="media-left">
        <label class="switch">
          <input
            @click.prevent="toggleAccepted"
            v-model="accepted"
            :disabled="hasErrors"
            type="checkbox"
            value="1"
          >
          <span class="slider round"></span>
        </label>
      </div>
      <div class="media-content">
        <div class="content">
          <p>
            <strong>{{ application.studentName }}</strong> <small>{{ application.studentEmail }}</small>
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
