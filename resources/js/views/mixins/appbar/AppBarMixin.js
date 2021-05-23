import { UserMixin } from "./UserMixin";
import { ThemeMixin } from "./ThemeMIxin";
import { BackupMixin } from "./BackupMixin";
import { TransactionDateMixin } from "./TransactionDateMixin";

export const AppBarMixin = {
  mixins: [
    UserMixin,
    ThemeMixin,
    BackupMixin,
    TransactionDateMixin
  ],
}
