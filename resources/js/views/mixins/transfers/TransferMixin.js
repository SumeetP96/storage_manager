import { EntryMixin } from "./EntryMixin"
import { RecordMixin } from "./RecordMixin"
import { DialogMixin } from "./DialogMixin"
import { DetailsMixin } from "./DetailsMixin"

export const TransferMixin = {
  mixins: [
    EntryMixin,
    RecordMixin,
    DialogMixin,
    DetailsMixin,
  ],

  data() {
    return {
      payload: {},
      backRoute: ''
    }
  }
}
