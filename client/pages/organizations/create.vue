<template>
    <div class="row">
        <div class="col-lg-8 m-auto">
            <card :title="$t('register')">
                <form @submit.prevent="" @keydown="form.onKeydown($event)">
                    <!-- Last name -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">Название организации</label>
                        <div class="col-md-7">
                            <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" type="text" name="name"
                                   class="form-control">
                            <has-error :form="form" field="name"/>
                        </div>
                    </div>
                </form>
            </card>
        </div>
    </div>
</template>

<script>
    import Form from 'vform';

    export default {
        head() {
            return {title: this.$t('register')};
        },

        data: () => ({
            form: new Form({
                name: ''
            })
        }),

        mounted() {
            this.form.post('/api/1/management/organizations');
        },

        methods: {
            async register() {
                // Register the user.
                const {data} = await this.form.post('/api/register');

                // Log in the user.
                const {data: {token}} = await this.form.post('/api/login');

                // Save the token.
                this.$store.dispatch('auth/saveToken', {token});

                // Update the user.
                await this.$store.dispatch('auth/updateUser', {user: data});

                // Redirect home.
                this.$router.push({name: 'home'});
            }
        }
    };
</script>
