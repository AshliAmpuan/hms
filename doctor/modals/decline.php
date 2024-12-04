<div id="exampleModaldecline<?php echo $row['reference']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered modal-md" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalCenterTitle">Confirm Reservation</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									</div>
									<div class="modal-body">
                                        <h4>Are you sure you want to decline this reservation?</h4>
                                    </div>
									<div class="modal-footer">
										<button type="button" class="btn  btn-secondary" data-dismiss="modal">Close</button>
                                        <a href="decline.php?reference=<?php echo $row['reference']; ?>" class="btn btn-primary">Decline Reservation</a>
									</div>
								</div>
							</div>
						</div>