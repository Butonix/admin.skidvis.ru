<template>
    <div class="row">
        <div class="col-lg-8 m-auto">
            <card :title="$t('register')">
                <form @submit.prevent="register" @keydown="form.onKeydown($event)">
                    <!-- Last name -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">{{ $t('l_name') }}</label>
                        <div class="col-md-7">
                            <input v-model="form.l_name" :class="{ 'is-invalid': form.errors.has('l_name') }" type="text" name="l_name"
                                   class="form-control">
                            <has-error :form="form" field="l_name"/>
                        </div>
                    </div>

                    <!-- First name -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">{{ $t('f_name') }}</label>
                        <div class="col-md-7">
                            <input v-model="form.f_name" :class="{ 'is-invalid': form.errors.has('f_name') }" type="text" name="f_name"
                                   class="form-control">
                            <has-error :form="form" field="f_name"/>
                        </div>
                    </div>

                    <!-- Middle name -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">{{ $t('m_name') }}</label>
                        <div class="col-md-7">
                            <input v-model="form.m_name" :class="{ 'is-invalid': form.errors.has('m_name') }" type="text" name="m_name"
                                   class="form-control">
                            <has-error :form="form" field="m_name"/>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">{{ $t('phone') }}</label>
                        <div class="col-md-7">
                            <input v-model="form.phone" :class="{ 'is-invalid': form.errors.has('phone') }" type="text" name="phone"
                                   class="form-control js-mask-phone">
                            <has-error :form="form" field="phone"/>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
                        <div class="col-md-7">
                            <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" type="email" name="email"
                                   class="form-control">
                            <has-error :form="form" field="email"/>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">{{ $t('password') }}</label>
                        <div class="col-md-7">
                            <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" type="password" name="password"
                                   class="form-control">
                            <has-error :form="form" field="password"/>
                        </div>
                    </div>

                    <!-- Password Confirmation -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">{{ $t('confirm_password') }}</label>
                        <div class="col-md-7">
                            <input v-model="form.password_confirmation" :class="{ 'is-invalid': form.errors.has('password_confirmation') }" type="password" name="password_confirmation"
                                   class="form-control">
                            <has-error :form="form" field="password_confirmation"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-7 offset-md-3 d-flex">
                            <!-- Submit Button -->
                            <v-button :loading="form.busy">
                                {{ $t('register') }}
                            </v-button>

                            <!-- GitHub Login Button -->
                            <login-with-github/>
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
                f_name:                '',
                l_name:                '',
                m_name:                '',
                email:                 '',
                phone:                 '',
                password:              '',
                password_confirmation: ''
            })
        }),

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
