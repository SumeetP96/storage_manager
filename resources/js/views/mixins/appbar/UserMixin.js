export const UserMixin = {
  methods: {
    changePassword() {
      window.location.href = "/user/password/change"
    },

    logout() {
      let csrfToken = this.getMeta('csrf-token')
      this.axios
        .post('/logout', { csrf_token: csrfToken })
        .then(response => this.$router.go(0))
    },

    getMeta(metaName) {
      const metas = document.getElementsByTagName('meta')

      for (let i = 0; i < metas.length; i++) {
        if (metas[i].getAttribute('name') == metaName) {
          return metas[i].getAttribute('content')
        }
      }

      return ''
    },
  }
}
