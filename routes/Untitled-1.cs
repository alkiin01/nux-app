var dset = ds.JobHead.Where(x=> x.RowMod =="A" || x.RowMod == "U").FirstOrDefault();

if(dset !=null && dset.JobType != "MNT" ){
if( dset.RowMod == "A"){
if(!dset.JobNum.Contains("SBC")){

  var Qty = dset.ProdQty;
  var Part = dset.PartNum;
  
  var topGroupId = Db.ECOGroup
    .Join(
        Db.ECOMtl,
        group => group.GroupID,
        mtl => mtl.GroupID,
        (group, mtl) => new { group.GroupID, mtl.PartNum, group.DueDate,group.GroupClosed, mtl.AltMethod }
    )
    .Where(joined => joined.PartNum == Part && joined.GroupClosed == false)
    .OrderByDescending(joined => joined.AltMethod)
    .Select(joined => new{ joined.GroupID, joined.AltMethod})
    .FirstOrDefault();
    
var dbMethod = Db.ECOMtl
    .Join(
        Db.PartWhse,
        b => b.MtlPartNum,
        a => a.PartNum,
        (b, a) => new { b, a }
    )
    .Join(
        Db.ECOGroup,
        joined => joined.b.GroupID,
        c => c.GroupID,
        (joined, c) => new { joined.b, joined.a, c }
    )
    .Join(
        Db.Warehse,
        joined => joined.a.WarehouseCode,
        w => w.WarehouseCode,
        (joined, w) => new { joined.b, joined.a, joined.c, w }
    )
    .Where(joined => joined.b.PartNum == dset.PartNum
        && joined.b.GroupID == topGroupId.GroupID 
        && joined.b.AltMethod == topGroupId.AltMethod 
        && joined.w.IsJobable_c == true 
        && joined.c.GroupClosed == false)
    .GroupBy(joined => new 
    { 
        joined.b.MtlPartNum, 
        joined.b.QtyPer 
    })
    .Select(grouped => new
    {
        MtlPartNum = grouped.Key.MtlPartNum,
        QtyPer = grouped.Key.QtyPer,
        Onhand = grouped.Sum(x => x.a.OnHandQty),
        JobDemandQty = grouped.Sum(x => x.a.JobDemandQty)
    })
    .ToList();



  if(dbMethod != null){
    bool status = true;
    string Desc = "Negative On Hand \n";
    var dbm = dbMethod.ToList();
   foreach(var item in dbm){
    var PartMtl = item.MtlPartNum;
    var QtyPer = Convert.ToDecimal(item.QtyPer);
    var OnHand = Convert.ToDecimal(item.Onhand);
    var QtyReq = QtyPer * Qty;
    var JobDemand = Convert.ToInt32(item.JobDemandQty);
    var QtyBener = OnHand - JobDemand;

      if(QtyReq > QtyBener)
      {
        status = false;
      }
     var qtyString = Convert.ToInt32(QtyReq);
      var OnHandString = Convert.ToInt32(OnHand);

      Desc += $"Mtl. Part {PartMtl} :  Requirement = {qtyString} | OnHand = {OnHandString}| OnHand After Job Demand = {QtyBener} | Other Job Demand Qty {JobDemand}  \n Method Directive Proccess";
    }
    if(status == false){
     dset.JobReleased = false;
         throw new Ice.BLException(Desc);
     }
  }
  }
}else if(dset.RowMod == "U" && dset.JobReleased == true && (dset.JobClosed == false || dset.JobComplete == false)){
if(!dset.JobNum.Contains("SBC")){
  var Qty = dset.ProdQty;
  var Part = dset.PartNum;

 var dbMethod = Db.JobMtl
    .Join(
        Db.PartWhse,
        jm => jm.PartNum,
        pw => pw.PartNum,
        (jm, pw) => new { jm, pw }
    )
    .Join(
        Db.Warehse,
        combined => combined.jm.WarehouseCode,
        w => w.WarehouseCode,
        (combined, w) => new { combined.jm, combined.pw, w }
    )
    .Where(x => x.jm.JobNum == dset.JobNum && x.w.IsJobable_c == true)
    .GroupBy(x => new { x.jm.PartNum, x.jm.RequiredQty, x.jm.QtyPer })
    .Select(g => new 
    {
        PartNum = g.Key.PartNum,
        RequiredQty = g.Key.RequiredQty,
        QtyPer = g.Key.QtyPer,
        JobDemand = g.Sum(x => x.pw.JobDemandQty),
        OnHand = g.Sum(x => x.pw.OnHandQty)
    })
    .ToList();

    
  if(dbMethod != null){
    bool status = true;
    statuses = true;
    string Desc = "Negative On Hand \n";
    var dbm = dbMethod.ToList();
     foreach(var item in dbm){
      var PartMtl = item.PartNum;
      var ReqQty = item.RequiredQty;
      var QtyPer = Convert.ToDecimal(item.QtyPer);
      var jobDemand = Convert.ToDecimal(item.JobDemand);
      var OnHand = Convert.ToDecimal(item.OnHand);
      //var QtyReq = Convert.ToInt32(QtyPer * ReqQty);
      
          
var joSBC = Db.JobMtl.Where(x => x.JobNum.StartsWith("SBC-") && x.JobComplete == false && x.PartNum == PartMtl)
    .Sum(x => (decimal?)x.RequiredQty) ?? 0;
      var jobdemands = jobDemand - joSBC;
      var QtyBener = OnHand - jobdemands;
        if(ReqQty >= QtyBener)
        {
          status = false;
          statuses = false;
        }
        var qtyString = Convert.ToInt32(ReqQty);
        var OnHandString = Convert.ToInt32(OnHand);
        var job = dset.JobNum;
        Desc += $"1. Job : {job} || Mtl. Part {PartMtl} :  Requirement = {qtyString} | OnHand = {OnHandString} \n Method Directive Proccess";
        Descs += $"1. Job : {job} || Mtl. Part {PartMtl} :  Requirement = {qtyString} | OnHand = {QtyBener} | Demand = {jobdemands} \n Method Directive Proccess";
      }
      if(statuses == false || status == false){
      throw new Ice.BLException(Descs);
       }
    }else{
            throw new Ice.BLException("Material Tidak Ditemukan");

    }
  }
}
}