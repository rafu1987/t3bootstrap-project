# # Brand name [BEGIN]

lib.brandName = TEXT
lib.brandName {
  value = Project
  stdWrap {
    typolink {
      parameter = {$t3bootstrap.basedomain.de}
      ATagParams = class="brand"
    }
  }
}

# # English
[globalVar = GP:L = 1]

lib.brandName {
  stdWrap {
    typolink {
      parameter = {$t3bootstrap.basedomain.en} 
    }
  }
}

[global]

# # Brand name [END]