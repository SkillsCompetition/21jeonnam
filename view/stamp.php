  <div class="emp_box" style="height: 70px;"></div>

  <div class="content">

    <div class="double">
      <div class="wrap">
        <div class="stamp_section">
          <div class="title">
            <h1>스탬프 카드 발급</h1>
          </div>

          <div class="btn_box full">
            <div class="btn" onclick="Modal.open('stampIssued')">발급하기</div>
          </div>
        </div>

        <div class="coupon_section">
          <div class="title">
            <h1>쿠폰코드</h1>
          </div>

          <div class="coupon btn_box">
            <div class="input_box">
              <label for="couponCode">쿠폰 코드</label>
              <input type="text" id="couponCode" name="couponCode">
            </div>

            <div class="btn" onclick="Stamp.checkCode()">확인하기</div>
          </div>
        </div>
      </div>
    </div>

    <div class="roulette_section">
      <div class="wrap">
        <div class="title">
          <h1>스탬프 이벤트 참여</h1>
        </div>

        <div class="roulette col-flex aic">
          <div class="input_box">
            <label for="addStampFile">스탬프 파일</label>
            <input type="text" class="upload_file rouletteFile" value="첨부파일" readonly>
            <div class="btn" onclick="Stamp.openFile('rouletteFile')">파일 찾기</div>
          </div>
          <div class="roulette_item">
            <div class="stick"></div>
            <canvas id="roulette" width="600" height="600"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal"></div>

  <template>

    <div class="stampIssued_modal">
      <div class="flex jcsb aic">
        <h3>스탬프 카드 발급</h3>

        <div class="close_btn">
          <i class="fa fa-close"></i>
        </div>
      </div>

      <hr>

      <div class="inputs">
        <div class="input_box">
          <label for="username">이름</label>
          <input type="text" id="username" name="username">
        </div>

        <div class="btn_box full">
          <div class="btn" onclick="Stamp.issued()">다운로드</div>
        </div>
      </div>
    </div>

    <div class="addStamp_modal">
      <div class="flex jcsb aic">
        <h3>스탬프 찍기</h3>

        <div class="close_btn">
          <i class="fa fa-close"></i>
        </div>
      </div>

      <hr>

      <div class="inputs">
        <div class="input_box">
          <label for="addStampFile">스탬프 파일</label>
          <input type="text" class="upload_file addStamp" value="첨부파일" readonly>
          <div class="btn" onclick="Stamp.openFile('addStamp')">파일 찾기</div>
        </div>

        <div class="btn_box full">
          <div class="btn" onclick="Stamp.addStamp()">스탬프 찍기</div>
        </div>
      </div>
    </div>

  </template>