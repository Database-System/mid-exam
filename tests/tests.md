# 如何使用phpUnit

## 檔名
檔名需為*Test.php結尾，如同現在tests裡的HelloTest.php,這裡規定要測什麼檔案就什麼名字開頭，例如要測: Utils那測試檔名為Utils_Test.php，
裡面的ClassName也需跟檔名一樣為Utils_Test

## 測試
將當前目錄切換到bin下
```shell
cd bin
```
### 執行
```shell
command.cmd test <接需要測試的檔名，無需副檔名>

