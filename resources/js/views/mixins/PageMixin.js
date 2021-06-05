export const PageMixin = {
  methods: {
    printPage(id, url) {
      const print = document.getElementById(id)
      print.setAttribute('src', url)
    }
  }
}
