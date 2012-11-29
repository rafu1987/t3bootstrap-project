lib.mainMenue = HMENU
lib.mainMenue {
  wrap = <ul class="nav">|</ul>
  1 = TMENU
  1 {
    NO = 1
    NO {
      allWrap = <li>|</li>
    }
    ACT < .NO
    ACT {
      allWrap = <li class="active">|</li>
    }
  }
}