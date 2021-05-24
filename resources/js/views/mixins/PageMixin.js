export const PageMixin = {
  methods: {
    printPage(url) {
      const print = document.getElementsByTagName('iframe')[0]
      print.setAttribute('src', url)
    }
  }
}
