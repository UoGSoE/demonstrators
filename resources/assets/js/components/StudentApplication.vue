<template>
		<article class="media">
			<div class="media-left">
				<toggle-button class="toggle-button" :value="accepted" @change="toggleAccepted" />
			</div>
			<div class="media-content">
				<div class="content">
					<p>
						<strong><span v-if="!application.academicSeen" class="icon"><i class="fa fa-star" title="New application"></i></span>{{ application.requestType }}</strong><br>
						<span class="icon"><i class="fa fa-id-card-o" title="email"></i></span>{{ application.studentName }}
						<span v-if="application.hasContract" class="icon"><i class="fa fa-file-text-o" title="Has contract"></i></span><br>
						<span v-if="application.studentDegreeLevel"><span class="icon"><i class="fa fa-graduation-cap" title="Degree level"></i></span>{{ application.studentDegreeLevel }}<br></span>
						<span class="icon"><i class="fa fa-envelope-o" title="email"></i></span>{{ application.studentEmail }}<br>
						<span v-if="application.studentNotes" class="icon"><i class="fa fa-comment-o" title="notes"></i></span>{{ application.studentNotes }}
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
