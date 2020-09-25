<template>
    <div class="columns is-centered">
        <div class="column is-three-quarters">
            <h3 class="title is-3">Add New Staff Member</h3>
            <div v-show="successMessage" style="display:none" class="notification is-success">
                {{successMessage}}
            </div>
            <div v-show="errorMessage" style="display:none" class="notification is-danger">
                {{errorMessage}}
            </div>
            <form>
                <label class="label">GUID</label>
                <div class="field has-addons">
                    <div class="control">
                        <input v-on:keyup="guidIsValid=false" v-model="user.username" name="username" type="text" class="input">
                    </div>
                    <div class="control">
                        <button v-on:click.prevent="lookup" class="button is-gla" type="button">Lookup</button>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Email</label>
                    <div class="control">
                        <input v-model="user.email" name="email" type="email" class="input">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Forenames</label>
                    <div class="control">
                        <input v-model="user.forenames" name="forenames" type="text" class="input">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Surname</label>
                    <div class="control">
                        <input v-model="user.surname" name="surname" type="text" class="input">
                    </div>
                </div>
                <button @click.prevent="saveRequest" :disabled="!isValid" class="button is-gla-success">Save</button>
            </form>
        </div>
    </div>
</template>
<script>
module.exports = {
    data() {
        return {
            user: {
                username: '',
                email: '',
                forenames: '',
                surname: '',
            },
            url: "/admin/users/lookup/",
            errorMessage: '',
            successMessage: '',
            guidIsValid: false,
        }
    },
    computed: {
        isValid: function() {
            return this.user.email && this.user.username && this.user.forenames && this.user.surname && this.guidIsValid;
        },
    },
    methods: {
        saveRequest() {
            axios.post('/admin/staff/new', this.user)
                .catch((error) => {
                    this.successMessage = "";
                    this.hasErrors = true;
                    console.log(error);
                })
                .then(() => {
                    this.successMessage = "Added " + this.user.forenames + " " + this.user.surname + ".";
                    this.clearRequest();
                }); 
        },
        lookup: function (e){
            self = this;
            axios.get( this.url + this.user.username, {validateStatus: function (status) {
                    return status < 400;
                }
            }
            ).then(function(response) {
                self.errorMessage = "";
                self.successMessage = "";
                self.user = response.data;
                self.guidIsValid = true;
            })
            .catch(function(error) {
                self.successMessage = "";
                self.errorMessage = error.response.data.message;
                self.guidIsValid = false;
            })
        },
        clearRequest() {
            this.user.username = '';
            this.user.email = '';
            this.user.forenames = '';
            this.user.surname = '';
        },
    }
};
</script>