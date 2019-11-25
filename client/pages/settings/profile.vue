<template>
    <card :title="$t('your_info')">
        <form @submit.prevent="update" @keydown="form.onKeydown($event)">
            <alert-success :form="form" :message="$t('info_updated')"/>

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

            <!-- Email -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
                <div class="col-md-7">
                    <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" type="email" name="email"
                           class="form-control">
                    <has-error :form="form" field="email"/>
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

            <!-- Submit Button -->
            <div class="form-group row">
                <div class="col-md-9 ml-md-auto">
                    <v-button :loading="form.busy" type="success">{{ $t('update') }}</v-button>
                </div>
            </div>
        </form>
    </card>
</template>

<script>
    import Form from 'vform';
    import {mapGetters} from 'vuex';

    export default {
        scrollToTop: false,

        head() {
            return {title: this.$t('settings')};
        },

        data: () => ({
            form: new Form({
                l_name: '',
                f_name: '',
                m_name: '',
                phone:  '',
                email:  ''
            })
        }),

        computed: mapGetters({
            user: 'auth/user'
        }),

        created() {
            // Fill the form with user data.
            this.form.keys().forEach(key => {
                this.form[key] = this.user[key];
            });
        },

        methods: {
            async update() {
                const {data} = await this.form.patch('/api/settings/profile');

                this.$store.dispatch('auth/updateUser', {user: data});
            }
        }
    };
</script>
