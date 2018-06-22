<template>
    <div>
        <button v-if="!editing" @click="editing=true" id="info-button" class="button is-pulled-right" >Student profile</button>
        <form v-if="editing" class="notes-form">
            <div class="field">
                <label class="label">Extra information</label>
                <div class="control">
                    <textarea v-model="currentStudent.notes" name="notes" class="textarea notes" placeholder="Add any extra information about your availability, skills, etc."></textarea>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button @click.prevent="saveNotes" class="button is-gla-success is-pulled-right notes-button">Save</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
module.exports = {
    props: ['student'],

    data () {
        return {
            currentStudent: this.student,
            editing: false,
        }
    },

    methods: {
        saveNotes () {
            var self = this;
            var url = '/student/'+this.currentStudent.id+'/profile';
            axios.post(url, {notes: this.currentStudent.notes})
            .then(function( data ) {
                self.editing = false;
            });
        }
    }
}
</script>