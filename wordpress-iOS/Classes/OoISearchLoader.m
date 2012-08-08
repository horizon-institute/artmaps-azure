//
//  POILoader.m
//  WordPress
//
//  Created by Shakir Ali on 11/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "JSONKit.h"
#import "OoISearchLoader.h"
#import "OoIAnnotation.h"
#import "ObjectOfInterest.h"
#import "RestAPIConnector.h"
#import "DataParser.h"

@interface OoISearchLoader ()
-(NSArray*)readOoIData:(NSArray*)data;
@end

@implementation OoISearchLoader

@synthesize delegate;

-(void)submitOoISearchRequestWithNEMapPoint:(CLLocationCoordinate2D)ne SWMapPoint:(CLLocationCoordinate2D)sw {
    [self initConnectionRequest];
    NSURLRequest *searchURLRequest = [self prepareOoISearchURLRequestWithNEMapPoint:ne SWMapPoint:sw loadMetaData:NO];
    [self submitURLRequest:searchURLRequest];
}

-(void)submitOoISearchRequestWithNEMapPoint:(CLLocationCoordinate2D)ne SWMapPoint:(CLLocationCoordinate2D)sw loadMetaData:(Boolean)loadMetaData{
    [self initConnectionRequest];
    NSURLRequest *searchURLRequest = [self prepareOoISearchURLRequestWithNEMapPoint:ne SWMapPoint:sw loadMetaData:loadMetaData];
    [self submitURLRequest:searchURLRequest];
}

-(NSURLRequest*)prepareOoISearchURLRequestWithNEMapPoint:(CLLocationCoordinate2D)ne SWMapPoint:(CLLocationCoordinate2D)sw loadMetaData:(Boolean)loadMetaData{
    NSNumber *number = [NSNumber numberWithDouble: ne.latitude * pow(10, 8)];
    NSString* neLatitude = [NSString stringWithFormat:@"%lld", [number longLongValue]];
    
    number = [NSNumber numberWithDouble: ne.longitude * pow(10, 8)];
    NSString* neLongitude = [NSString stringWithFormat:@"%lld", [number longLongValue]];
    
    number = [NSNumber numberWithDouble: sw.latitude * pow(10, 8)];
    NSString* swLatitude = [NSString stringWithFormat:@"%lld", [number longLongValue]];
    
    number = [NSNumber numberWithDouble: sw.longitude * pow(10, 8)];
    NSString *swLongitude = [NSString stringWithFormat:@"%lld", [number longLongValue]];

    NSString* searchUrl = [[RestAPIConnector sharedInstance] getOOISearchRequestURLWithNELatitude:neLatitude SWLatitude:swLatitude NELongitude:neLongitude SWLongitude:swLongitude];
    NSLog(@"Search URL %@", searchUrl);
    NSURL* url = [NSURL URLWithString:searchUrl];
    NSURLRequest *urlRequest = [NSURLRequest requestWithURL:url];
    //NSURLRequest *urlRequest = [NSURLRequest requestWithURL:url cachePolicy:NSURLRequestReloadIgnoringCacheData timeoutInterval:60];

    return urlRequest;
}

#pragma mark NSURLConnectionDelegate functions.
- (void)connectionDidFinishLoading:(NSURLConnection *)connection{
    [super connectionDidFinishLoading:connection];
    NSArray *data= [self.jsonData objectFromJSONData];
    NSArray *objectOfInterests = [self readOoIData:data];
    if ([self.delegate respondsToSelector:@selector(OoISearchLoader:didLoadOoIData:)])
        [delegate OoISearchLoader:self didLoadOoIData:objectOfInterests];
}

- (void)connection:(NSURLConnection *)connection didFailWithError:(NSError *)error{
    [super connection:connection didFailWithError:error];
}

-(NSArray*)readOoIData:(NSArray*)data{
    NSMutableArray* objectOfInterests = [[NSMutableArray alloc] initWithCapacity:data.count];
    for (int i = 0; i < data.count; i++){
        ObjectOfInterest* ooi = [DataParser getOoIFromDictionary:[data objectAtIndex:i]];
        [objectOfInterests addObject:ooi];
    }
    return [objectOfInterests autorelease];
}

-(void)dealloc{
    delegate = nil;
    [super dealloc];
}
@end
