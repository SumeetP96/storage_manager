export const ThemeMixin = {
  data() {
    return {
      theme: ''
    }
  },

  methods: {
    toggleApplicationTheme(theme) {
      if (theme == 'light') localStorage.setItem('dark_theme', false)
      else if (theme == 'dark') localStorage.setItem('dark_theme', true)
      this.theme = theme
      this.setApplicationTheme()
    },

    setApplicationTheme() {
      let theme = (localStorage.getItem('dark_theme') == 'true') ? 'dark' : 'light'
      if (theme == 'dark') {
        document.body.style.backgroundColor = '#121212';
        this.$vuetify.theme.dark = true
      } else {
        this.$vuetify.theme.dark = false
        document.body.style.backgroundColor = '#ECEFF1';
      }
      this.theme = theme
    }
  }
}
