//
//  HIPOIViewController.m
//  WordPress
//
//  Created by Shakir Ali on 11/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "HIOoIViewController.h"
#import "JSONKit.h"
#import "OoIAnnotation.h"
#import "OoIAnnotationView.h"
#import "ObjectOfInterest.h"
#import "OoILocation.h"
#import "ExperienceConfigurer.h"
#import "OoIMetaLoader.h"
#import "MetaTableViewController.h"
#import "PostMapLocation.h"
#import "MapHelper.h"
#import "REVClusterAnnotationView.h"
#import "MetaListTableViewController.h"

@interface HIOoIViewController ()
@property (nonatomic, retain) CLLocationManager *locationManager;
@property (nonatomic, retain) OoISearchLoader *ooiSearchLoader;
@property (nonatomic, retain) OoIMetaLoader *ooiMetaLoader;
@property (nonatomic, retain) OoIMeta *selectedOoiMeta;

-(MKAnnotationView*)addExistingOoIAnnotationView:(MKMapView*)mapView viewForAnnotation:(OoILocation*)annotation;
//-(MKAnnotationView*)addNewOoIAnnotationView:(MKMapView*)mapView viewForAnnotation:(OoILocation*)annotation;
//-(void)addNewOoIBtn;
//-(IBAction)addNewOoIBtnClick:(id)sender;
//-(OoILocation*)getNewOoIAnnotation:(CLLocationCoordinate2D)location;
-(void)setupLocationManager;
-(void)postViewDismissed:(id)notification;
@end

@implementation HIOoIViewController

@synthesize mapViewControl;
@synthesize locationManager;
@synthesize ooiSearchLoader;
@synthesize existingObjectOfInterests;
@synthesize ooiMetaLoader;
@synthesize selectedOoiMeta;

const NSInteger DETAIL_OoIMETA_BUTTON_TAG = 1;

#define MINIMUM_CLUSTER_LEVEL 419430
#define LATITUDE_DELTA_OFFSET 10


#pragma mark initialization

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        
    }
    return self;
}

- (void)viewDidLoad
{
    //[self addNewOoIBtn];
    [self setupLocationManager];
    [self setupOoISearchLoader];
    [self setupOoIMetaLoader];
    [self setupNavigationBar];
    existingObjectOfInterests = [[NSMutableArray alloc] initWithCapacity:0];
    mapViewControl.delegate = self;
    mapViewControl.minimumClusterLevel = MINIMUM_CLUSTER_LEVEL;
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(postViewDismissed:) name:@"PostEditorDismissed" object:nil];
    [super viewDidLoad];
}

-(void)setupLocationManager{
    // Create location manager with filters set for battery efficiency.
	locationManager = [[CLLocationManager alloc] init];
	locationManager.delegate = self;
	locationManager.distanceFilter = kCLLocationAccuracyHundredMeters;
	locationManager.desiredAccuracy = kCLLocationAccuracyBest;
	// Start updating location changes.
	[locationManager startUpdatingLocation];
}

-(void)setupOoISearchLoader{
    ooiSearchLoader = [[OoISearchLoader alloc] init];
    ooiSearchLoader.delegate = self;
}


-(void)setupOoIMetaLoader{
    ooiMetaLoader = [[OoIMetaLoader alloc] init];
    ooiMetaLoader.delegate = self;    
}

-(void)setupNavigationBar{
    self.navigationItem.title = [[[ExperienceConfigurer sharedInstance] currentExperience] getTitleForOoIList];
}

#pragma mark - memory management.

- (void)viewDidUnload
{
    self.mapViewControl = nil;
    self.locationManager.delegate = nil;
	self.locationManager = nil;
    self.ooiSearchLoader.delegate = nil;
    self.ooiSearchLoader = nil;
    self.ooiMetaLoader.delegate = nil;
    self.ooiMetaLoader = nil;
    self.selectedOoiMeta = nil;
    //[newObjectOfInterest release];
    //newObjectOfInterest = nil;
    [existingObjectOfInterests release];
    existingObjectOfInterests = nil;
    [[NSNotificationCenter defaultCenter] removeObserver:self];
    [super viewDidUnload];
}

-(void)dealloc{
    [mapViewControl release];
    //[newObjectOfInterest release];
    [existingObjectOfInterests release];
    [locationManager release];
    [ooiSearchLoader release];
    [ooiMetaLoader release];
    [selectedOoiMeta release];
    [[NSNotificationCenter defaultCenter] removeObserver:self];
    [super dealloc];
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

/*
-(void)addNewOoIBtn{
    UIBarButtonItem *rightBarButton = [[UIBarButtonItem alloc] initWithTitle:@"Add Point" style:UIBarButtonItemStylePlain target:self action:@selector(addNewOoIBtnClick:)];
    self.navigationItem.rightBarButtonItem = rightBarButton;
    [rightBarButton release];
}


-(IBAction)addNewOoIBtnClick:(id)sender{
    OoILocation* ooi = [self getNewOoIAnnotation:mapViewControl.region.center];
    [mapViewControl addAnnotation:ooi];
}
*/

#pragma mark OoISearchLoaderDelegate
-(void)OoISearchLoader:(OoISearchLoader *)loader didLoadOoIData:(NSArray *)objectOfInterests{
    [self.existingObjectOfInterests addObjectsFromArray:objectOfInterests];
    [self addAnnotationsToMap:existingObjectOfInterests];
    [UIApplication sharedApplication].networkActivityIndicatorVisible = NO;
}

-(void)addAnnotationsToMap:(NSArray*)objectOfInterests{
    NSMutableArray* annotations = [[NSMutableArray alloc] initWithCapacity:0];
    for (ObjectOfInterest *objectOfInterest in objectOfInterests){
        [annotations addObjectsFromArray:objectOfInterest.ooiLocations];         
    }
    if ( annotations.count > 0 )
        [mapViewControl addAnnotations:annotations];
    [annotations release];
}

-(void)OoISearchLoader:(OoISearchLoader *)loader didFailWithError:(NSError *)error{
    [UIApplication sharedApplication].networkActivityIndicatorVisible = NO;
}

/*
-(OoILocation*)getNewOoIAnnotation:(CLLocationCoordinate2D)location{
    if (newObjectOfInterest == nil){
        newObjectOfInterest = [[ObjectOfInterest alloc] init];
    }
    //create location
    OoILocation *ooiLocation = [[OoILocation alloc] init];
    ooiLocation.location_id = 0;
    ooiLocation.latitude = location.latitude;
    ooiLocation.longitude = location.longitude;
    ooiLocation.parent = newObjectOfInterest;
    
    //copy previous locations.
    NSMutableArray* locations = [[NSMutableArray alloc] initWithCapacity:0];
    if (newObjectOfInterest.ooiLocations.count > 0 )
        [locations addObjectsFromArray:newObjectOfInterest.ooiLocations];
    //add new location.
    [locations addObject:ooiLocation];
    //set locations.
    [newObjectOfInterest setOoiLocations:locations];
    [locations release];
    
    return [ooiLocation autorelease]; 
}
 */

#pragma mark MKMapViewDelegate
-(MKAnnotationView *)mapView:(MKMapView *)mapView viewForAnnotation:(id<MKAnnotation>)annotation{
    MKAnnotationView* annotationView = nil;
    if ([annotation isKindOfClass:[MKUserLocation class]]){
        annotationView = nil;
    }
    /*
    else if ([annotation isKindOfClass:[OCAnnotation class]]) {
        OCAnnotation* ocAnnotation = (OCAnnotation*)annotation;
        annotationView = [self addClusterAnnotationView:mapView viewForAnnotation:ocAnnotation];
    }*/
    else if ([annotation isKindOfClass:[REVClusterPin class]]){
        REVClusterPin* ooiAnnotation = (REVClusterPin*)annotation;
        if ([ooiAnnotation nodeCount] > 0){
            annotationView = [self addClusterAnnotationView:mapView viewForAnnotation:ooiAnnotation];
        }else{
            OoILocation* ooiAnnotation = (OoILocation*)annotation;
            if (ooiAnnotation.location_id > 0){  
                annotationView = [self addExistingOoIAnnotationView:mapView viewForAnnotation:ooiAnnotation];
            }
        }
        /*
        else{
            annotationView = [self addNewOoIAnnotationView:mapView viewForAnnotation:ooiAnnotation];
        }*/
    }
    return annotationView;
}

-(MKAnnotationView*)addExistingOoIAnnotationView:(MKMapView*)mapView viewForAnnotation:(OoILocation*)annotation{
    static NSString* OoIAnnotationID = @"ExistingPOIAnnotationID";
    OoIAnnotationView* annotationView = (OoIAnnotationView*)[mapView dequeueReusableAnnotationViewWithIdentifier:OoIAnnotationID];
    if (annotationView == nil){
        annotationView = [[[OoIAnnotationView alloc] initWithAnnotation:annotation reuseIdentifier:OoIAnnotationID] autorelease];
     }else {
        annotationView.annotation = annotation;
    }
    return annotationView;
}

-(MKAnnotationView*)addClusterAnnotationView:(MKMapView*)mapView viewForAnnotation:(REVClusterPin*)annotation{
    static NSString *clusterID = @"ClusterView";
    
    REVClusterAnnotationView* annotationView = (REVClusterAnnotationView*)[mapView dequeueReusableAnnotationViewWithIdentifier:clusterID];
    if (annotationView == nil){
        annotationView = [[[REVClusterAnnotationView alloc] initWithAnnotation:annotation reuseIdentifier:clusterID] autorelease];
        annotationView.image = [UIImage imageNamed:@"cluster.png"];
    }else {
        annotationView.annotation = annotation;
    }
    
    if ([MapHelper getMapZoomLevel:mapView] == MAX_GOOGLE_ZOOM_LEVEL - 1){
        annotationView.canShowCallout = YES;
        annotation.title = [NSString stringWithFormat:@"%@ : %d", [[[ExperienceConfigurer sharedInstance] currentExperience] getTitleForOoIList],[annotation nodeCount]];
    }else {
        annotationView.canShowCallout = NO;
    }
    
    [annotationView setClusterText:
     [NSString stringWithFormat:@"%i",[annotation nodeCount]]];
    return annotationView;    
}

/*
-(MKAnnotationView*)addNewOoIAnnotationView:(MKMapView*)mapView viewForAnnotation:(OoILocation*)annotation{
    static NSString* OoIAnnotationID = @"NewPOIAnnotationID";
    MKPinAnnotationView* annotationView = (MKPinAnnotationView*)[mapView dequeueReusableAnnotationViewWithIdentifier:OoIAnnotationID];
    if (annotationView == nil){
        annotationView = [[[MKPinAnnotationView alloc] initWithAnnotation:annotation reuseIdentifier:OoIAnnotationID] autorelease];
        annotationView.draggable = YES;
        annotationView.canShowCallout = YES;
        annotationView.pinColor = MKPinAnnotationColorGreen;
    }else {
        annotationView.annotation = annotation;
        
    }
    return annotationView;
}
*/

-(void)mapView:(MKMapView *)mapView didSelectAnnotationView:(MKAnnotationView *)view{
    if ([view isKindOfClass:[OoIAnnotationView class]]){
        [self locationAnnotationViewSelected:(OoIAnnotationView*)view];
    }else if ([view isKindOfClass:[REVClusterAnnotationView class]]) {
        [self clusterAnnotationViewSelected:(REVClusterAnnotationView*)view];
    }
}

-(void)locationAnnotationViewSelected:(OoIAnnotationView*)annotationView{
    self.selectedOoiMeta = nil;
    OoILocation* location = (OoILocation*)annotationView.annotation;
    [self setAnnotationViewLabel:(OoIAnnotationView*)annotationView text:NSLocalizedString(@"Loading...", nil)];
    [self submitOoIMetaRequestForAnnotation:location];
}

-(void)clusterAnnotationViewSelected:(REVClusterAnnotationView*)annotationView{
    if ([MapHelper getMapZoomLevel:mapViewControl] < MAX_GOOGLE_ZOOM_LEVEL - 1){
        CLLocationCoordinate2D centerCoordinate = [(REVClusterPin *)annotationView.annotation coordinate];
        CLLocationDegrees newLatitudeDelta = mapViewControl.region.span.latitudeDelta/2.0;
        CLLocationDegrees newLongitudeDelta = mapViewControl.region.span.longitudeDelta/2.0;
        
        MKCoordinateSpan newSpan = MKCoordinateSpanMake(newLatitudeDelta, newLongitudeDelta); 
        MKCoordinateRegion region = MKCoordinateRegionMake(centerCoordinate, newSpan);
        [mapViewControl setRegion:region animated:YES];
    }
}

-(void)submitOoIMetaRequestForAnnotation:(OoILocation*)location{
    ooiMetaLoader.refObjID = [[NSNumber alloc]initWithInt:location.location_id];
    NSNumber *ooi_ID = [NSNumber numberWithInt:location.parent.OoI_id];
    [ooiMetaLoader submitOoIMetaRequestWithID:ooi_ID];
}

- (void)mapView:(MKMapView *)mapView regionDidChangeAnimated:(BOOL)animated{
    //[mapView removeOverlays:mapView.overlays];
    [self submitOOiSearchRequest];
}

-(void)submitOOiSearchRequest{
    [UIApplication sharedApplication].networkActivityIndicatorVisible = YES;
    [mapViewControl removeAnnotations:mapViewControl.annotations];
    [self.existingObjectOfInterests removeAllObjects];
    CLLocationCoordinate2D ne = [MapHelper calculateNEMapCoordinates:mapViewControl];
    CLLocationCoordinate2D sw = [MapHelper calculateSWMapCoordinates:mapViewControl];
    [ooiSearchLoader submitOoISearchRequestWithNEMapPoint:ne SWMapPoint:sw];
}

-(void)mapView:(MKMapView *)mapView annotationView:(MKAnnotationView *)view calloutAccessoryControlTapped:(UIControl *)control{
    if ([view isKindOfClass:[OoIAnnotationView class]]){
        [self calloutForLocationAnnotationView:(OoIAnnotationView*)view calloutAccessoryControlTapped:control];
    }
    else if ([view isKindOfClass:[REVClusterAnnotationView class]]) {
        [self calloutForClusterAnnotationView:(REVClusterAnnotationView*)view calloutAccessoryControlTapped:control];
    }
}

-(void)calloutForLocationAnnotationView:(OoIAnnotationView*)annotationView calloutAccessoryControlTapped:(UIControl *)control{
    if (control.tag == DETAIL_OoIMETA_BTN_TAG){
        if (self.selectedOoiMeta){
            [self showMetaTableViewControllerForAnnotation:[annotationView annotation] meta:self.selectedOoiMeta];
        }
    }
}

-(void)calloutForClusterAnnotationView:(REVClusterAnnotationView*)annotationView calloutAccessoryControlTapped:(UIControl *)control{
    REVClusterPin* clusterAnnotation = [annotationView annotation];
    if (control.tag == CLUSTER_DETAIL_BTN_TAG){
        NSArray* locations = clusterAnnotation.nodes;
        NSMutableArray* ooI_IDs = [[NSMutableArray alloc] initWithCapacity:[locations count]];
        for ( OoILocation* location in locations){
            [ooI_IDs addObject:[[[NSNumber alloc]initWithInt:location.parent.OoI_id] autorelease]];
        }
        [self showMetaListTableViewControllerForOoIs:ooI_IDs atLocation:[locations objectAtIndex:0]];        
    }
}

/*
- (MKOverlayView *)mapView:(MKMapView *)mapView viewForOverlay:(id <MKOverlay>)overlay{
    MKCircle *circle = overlay;
    MKCircleView *circleView = [[MKCircleView alloc] initWithCircle:overlay];
    
    if ([circle.title isEqualToString:@"background"])
    {
        circleView.fillColor = [UIColor yellowColor];
        circleView.alpha = 0.25;
    }
    else if ([circle.title isEqualToString:@"helper"])
    {
        circleView.fillColor = [UIColor redColor];
        circleView.alpha = 0.25;
    }
    else
    {
        circleView.strokeColor = [UIColor blackColor];
        circleView.lineWidth = 0.5;
    }
    
    return [circleView autorelease];
}
*/

-(void)showMetaTableViewControllerForAnnotation:(OoILocation*)annotation meta:(OoIMeta*)ooiMeta{
    MetaTableViewController *metaTableViewController = [[MetaTableViewController alloc] initWithNibName:@"MetaTableViewController" bundle:nil];
    metaTableViewController.ooiMeta = selectedOoiMeta;
    //metaTableViewController.postComposeDelegate = self;
    metaTableViewController.postMapLocation = [self getPostMapLocationForAnnotation:annotation];
    [self.navigationController pushViewController:metaTableViewController animated:YES];
    [metaTableViewController release];
}

-(void)showMetaListTableViewControllerForOoIs:(NSArray*)ooI_IDs atLocation:(OoILocation*)location{
    MetaListTableViewController *metaListTableViewController = [[MetaListTableViewController alloc] initWithNibName:@"MetaListTableViewController" bundle:nil];
    metaListTableViewController.ooI_IDs = ooI_IDs;
    metaListTableViewController.postMapLocation = [self getPostMapLocationForAnnotation:location];
    [self.navigationController pushViewController:metaListTableViewController animated:YES];
    [metaListTableViewController release];
}

-(PostMapLocation*)getPostMapLocationForAnnotation:(OoILocation*)annotation{
    PostMapLocation *postMapLocation = [[PostMapLocation alloc] init];
    [postMapLocation setCurrentZoomLevelForMap:mapViewControl];
    postMapLocation.center = annotation.coordinate;
    return ([postMapLocation autorelease]);
}

#pragma mark - OoIMetaLoaderDelegate
-(void)OoIMetaLoader:(OoIMetaLoader *)loader didLoadOoIMeta:(OoIMeta *)ooiMeta{
    OoILocation* location = [self findLocationAnnotationInMapView:mapViewControl objID:loader.refObjID];
    if (location != nil){
        OoIAnnotationView *view = (OoIAnnotationView*)[mapViewControl viewForAnnotation:location];
        [self setAnnotationViewLabel:view text:ooiMeta.artist];
        self.selectedOoiMeta = ooiMeta;
    }
}

-(void)OoIMetaLoader:(OoIMetaLoader *)loader didFailWithError:(NSError *)error{
    OoILocation* location = [self findLocationAnnotationInMapView:mapViewControl objID:loader.refObjID];
    if (location != nil){
        OoIAnnotationView *view = (OoIAnnotationView*)[mapViewControl viewForAnnotation:location];
        [self setAnnotationViewLabel:view text:NSLocalizedString(@"Loading failed", nil)];
        self.selectedOoiMeta = nil;
    }
}

-(void)setAnnotationViewLabel:(OoIAnnotationView*)annotationView text:(NSString*)text{
    UILabel *label = (UILabel*)[annotationView.leftCalloutAccessoryView viewWithTag:OOIANNOTATION_LABEL];
    label.text = text;
}

-(OoILocation*)findLocationAnnotationInMapView:(MKMapView*)mapView objID:(NSNumber*)objID{
    OoILocation* locationAnnotation = nil;
    NSArray* selectedAnnotations = mapView.selectedAnnotations;
    for (OoILocation* location in selectedAnnotations){
        if (location.location_id == [objID intValue]){
            locationAnnotation = location;
            break;
        }
    }
    return locationAnnotation;
}


#pragma mark - CLLocationManagerDelegate
- (void)locationManager:(CLLocationManager *)manager didFailWithError:(NSError *)error {
	NSLog(@"didFailWithError: %@", error);
}

- (void)locationManager:(CLLocationManager *)manager didUpdateToLocation:(CLLocation *)newLocation fromLocation:(CLLocation *)oldLocation {
		
	// Work around a bug in MapKit where user location is not initially zoomed to.
    // Code from Apple Regions example.
    if (oldLocation == nil) {
		// Zoom to the current user location.
		MKCoordinateRegion userLocation = MKCoordinateRegionMakeWithDistance(newLocation.coordinate, 6000.0, 6000.0);
		[mapViewControl setRegion:userLocation animated:YES];
	}
}

-(void)postViewDismissed:(id)notification{
    [self.navigationController popToRootViewControllerAnimated:YES];
}

@end
