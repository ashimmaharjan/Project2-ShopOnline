<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" indent="yes"/>
    
    <xsl:template match="/">
        <html>
            <head>
                <title>Auction Report</title>
                <style>
                  .report-wrapper{
                      width: auto;
                      height: auto;
                      padding: 50px 30px;
                      border: 3px dashed #6b7280;
                      margin-top: 40px;
                      border-radius: 20px;
                  }
                    .table-holder {
                      width: 100%;
                      overflow-x: auto; 
                    }
                    table {
                        border-collapse: collapse;
                        width: auto;
                        color: #4b5563;
                    }
                    th, td {
                        border: 1px solid black;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #f2f2f2;
                    }        

                    h2 {
                      color: #0369a1;
                      margin-bottom: 20px;
                    }
                    .total-stats {
                      padding: 10px 30px;
                      background: #8a7356;
                      color: white;
                      display: grid;
                      grid-template-columns: repeat(3, minmax(0, 1fr));
                      text-align: center;
                    }            
                </style>
            </head>
            <body>
              <div class="report-wrapper">
                  <h2>Auction Report</h2>
                  <div class="table-holder">
                   <table>
                      <tr>
                          <th>Item Number</th>
                          <th>Item Name</th>
                          <th>Category</th>
                          <th>Start Price</th>
                          <th>Reserve Price</th>
                          <th>Buy It Now Price</th>
                          <th>Start Date</th>
                          <th>Start Time</th>
                          <th>Duration (mins)</th>
                          <th>Seller ID</th>
                          <th>Bidder ID</th>
                          <th>Current Bid Price</th>
                          <th>Status</th>
                      </tr>
                      <xsl:apply-templates select="//item[Status='sold' or Status='failed']"/>
                  </table>
                  </div>

                  <div class="total-stats">
                    <p>Total Sold Items: <b><xsl:value-of select="count(//item[Status='sold'])"/></b></p>
                    <p>Total Failed Items: <b><xsl:value-of select="count(//item[Status='failed'])"/></b></p>
                    <p>Revenue: <b>$<xsl:value-of select="format-number((sum(//item[Status='sold']/LatestBid/CurrentBidPrice) * 0.03) + (sum(//item[Status='failed']/ReservePrice) * 0.01), '#0.00')"/></b></p>
                  </div>
                 
              </div>
            </body>
        </html>
    </xsl:template>
    
    <xsl:template match="item">
        <tr>
            <td><xsl:value-of select="ItemNumber"/></td>
            <td><xsl:value-of select="ItemName"/></td>
            <td><xsl:value-of select="Category"/></td>
            <td><xsl:value-of select="StartPrice"/></td>
            <td><xsl:value-of select="ReservePrice"/></td>
            <td><xsl:value-of select="BuyItNowPrice"/></td>
            <td><xsl:value-of select="StartDate"/></td>
            <td><xsl:value-of select="StartTime"/></td>
            <td><xsl:value-of select="Duration"/></td>
            <td><xsl:value-of select="SellerID"/></td>
            <td><xsl:value-of select="LatestBid/BidderID"/></td>
            <td><xsl:value-of select="LatestBid/CurrentBidPrice"/></td>
            <td><xsl:value-of select="Status"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
